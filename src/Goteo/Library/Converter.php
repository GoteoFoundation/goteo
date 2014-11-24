<?php

namespace Goteo\Library;

/*
 * Clase para mantener una cache de rates
 *
 * necesita un array de divisas ( desde Library\Currency )
 *
*/

use Goteo\Library\Mail,
    Goteo\Library\Cacher;

class Converter {


    const
        ECB_URL = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml',
        TMC_URL =  'http://themoneyconverter.com/rss-feed/$BASE$/rss.xml',
        TEXT_RequestRatesFailed = 'Unable to fetch the currency rates feed.',
        TEXT_AmountMustBeNumeric = 'The given amount to convert must be numeric.';

    private $debug = false; // activate on dev phase only
    private $cache = null;
    private $source = 'ecb'; // 'tmc' = themoneyconverter.com

    public $currencies; // gets those from Currency Library

    public function __construct(Array $currencies) {
        $this->currencies = $currencies;
        //TODO: mejor pasar la dependencia por el constructor?
        $this->cache = new Cacher('currency');
    }


    /**
     *  Do a cUrl request
     */
    private function doRequest($url, $debug = false)
    {
        if ($debug) echo $url.'<hr />';

        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $curl, CURLOPT_USERAGENT, 'Goteo.org Currency Getter');

        $result = curl_exec( $curl );

        curl_close( $curl );

        if ($debug)  echo htmlentities($result);
        if ($debug) die;

        return array('body' => $result);
    }

    /**
     * Request and parse depending on source
     *
     * @return mixed $rates
     */
    private function getData ($base) {

        // European central bank is just for euro
        if ($base != 'EUR' && $this->source == 'ecb') {
            $this->source = 'tmc';
        }

        $rates = array();

        // Get the raw data
        switch ($this->source) {
            case 'ecb': // european central bank
                //the file is updated daily between 2.15 p.m. and 3.00 p.m. CET
                $feed_url = self::ECB_URL;

                if( ini_get('allow_url_fopen') ) {
                    $XML=simplexml_load_file($feed_url);
                    $response['body'] = $XML;
                } else {
                    $response = self::doRequest($feed_url, true); //@TODO
                    $file = '<?xml version="1.0" encoding="UTF-8" ?> '.$response['body'];
                    @$XML=simplexml_load_string($file);
                }

                break;

            case 'tmc': // the money converter . com
                // feed request
                $feed_url = str_replace('$BASE$', $base, self::TMC_URL);
                $response = self::doRequest($feed_url, true); //@TODO
                $file = '<?xml version="1.0" encoding="UTF-8" ?> '.$response['body'];
                @$XML=simplexml_load_string($file);
                break;
        }

        // verify data
        if (!$XML) {
            // mail de aviso
            $mailHandler = new Mail();
            $mailHandler->to = \GOTEO_FAIL_MAIL;
            $mailHandler->subject = 'No coge divisas '.$this->source;
            $mailHandler->content = 'Library\Currency->getData  no obtiene feed desde '.$feed_url.' la respuesta es de '.strlen($response['body']);
            $mailHandler->html = false;
            $mailHandler->template = null;
            $mailHandler->send();
            unset($mailHandler);

            return null;
        }

        if ($this->debug) {
            echo $feed_url;
            echo \trace($XML);
            die;
        }

        // parse the data
        switch ($this->source) {
            case 'ecb': // european central bank
                foreach($XML->Cube->Cube->Cube as $rate){

                    if ( empty($rate["rate"]) ) {
                        // mail de aviso
                        $mailHandler = new Mail();
                        $mailHandler->to = \GOTEO_FAIL_MAIL;
                        $mailHandler->subject = 'No coge divisas';
                        $mailHandler->content = 'Library\Currency->getData  no obtiene valor para '.$rate["currency"].' <pre>'.print_r($rate , 1).'</pre>';
                        $mailHandler->html = false;
                        $mailHandler->template = null;
                        $mailHandler->send();
                        unset($mailHandler);

                        continue;
                    }

                    //echo '1&euro;='.$rate["rate"].' '.$rate["currency"].'<br/>';
                    //var_dump($rate);
                    $curId = (string) $rate["currency"];
                    $curVal = (string) $rate["rate"];
                    //var_dump($curId);
                    //var_dump($curVal);
                    $rates[$curId] = $curVal;
                    //var_dump($rates[$curId]);

                }

                break;

            case 'tmc': // the money converter . com
                foreach($XML->channel->item as $rate) {
                    $tc = explode('/',$rate->title);
                    $ex = explode(' = ',$rate->description);
                    list($val,$name) = explode(' ',$ex[1],2);

                    if ( empty($val) ) {
                        // mail de aviso
                        $mailHandler = new Mail();
                        $mailHandler->to = \GOTEO_FAIL_MAIL;
                        $mailHandler->subject = 'No coge divisas';
                        $mailHandler->content = 'Library\Converter->getData  no obtiene valor para '.$tc[0].' <pre>'.print_r($rate , 1).'</pre>';
                        $mailHandler->html = false;
                        $mailHandler->template = null;
                        $mailHandler->send();
                        unset($mailHandler);

                        continue;
                    }

                    $rates[$tc[0]] = $val;
                }
                break;
        }

        return $rates;
    }


    /**
     * fetch currency rates based on given base currency
     * @param string $base currency ISO code
     * @param int $ttl caching time in seconds
     * @return array
     */
    public function getRates($base='EUR', $ttl=86400)
    {


        // check cache (if not debugging)
        if ($this->cache && !$this->debug) {
            $key = $this->cache->getKey($base, 'rates');
            $rates = $this->cache->retrieve($key);
        }

        if($rates === false) {
            $rates = $this->getData($base);
            // sets cache
            if($this->cache) $this->cache->store($key, $rates, $ttl);
        }

        return $rates;

    }


    /**
     * @param int|float $amount
     * @param string    $from    currency ISO code
     * @param string    $to      currency ISO code
     * @param int|bool  $decimal precision
     * @return float|bool
     */
    public function convert($amount, $from, $to)
    {
        if (!is_float($amount) && !is_numeric($amount)) {
            trigger_error(self::TEXT_AmountMustBeNumeric);

            // mail de aviso
            $mailHandler = new Mail();
            $mailHandler->to = \GOTEO_FAIL_MAIL;
            $mailHandler->subject = 'Conversión de un no-numerico';
            $mailHandler->content = 'Library\Converter->convert recibe un valor no-numerico y no se puede convertir. <hr /><pre>'.print_r($_SESSION).'</pre> <hr /><pre>'.print_r($_SERVER).'</pre>';
            $mailHandler->html = true;
            $mailHandler->template = null;
            $mailHandler->send();
            unset($mailHandler);

            return false;
        }
        if($from == $to) return $amount;

        $rates = $this->getRates($from);
        $result = $rates[$to] * $amount;

        return $result;
    }

    /**
     * Invalidates the cache
     */
    public function cleanCache() {
        if($this->cache) $this->cache->clean();
    }
}
