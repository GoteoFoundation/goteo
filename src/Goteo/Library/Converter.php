<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Library;

/*
 * Clase para mantener una cache de rates
 */

use Goteo\Model\Mail;
use Goteo\Application\Config;
use Goteo\Library\Cacher;

class Converter {


    const
        ECB_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml',
        FLR_URL =  'https://www.floatrates.com/daily/$BASE$.xml',
        TEXT_RequestRatesFailed = 'Unable to fetch the currency rates feed.';

    private $debug = false; // activate on dev phase only
    private $cache = null;
    private $source = 'ecb'; // 'flr' = floatrates.com

    public function __construct() {
        //TODO: mejor pasar la dependencia por el constructor?
        $this->cache = new Cacher('currency');
    }


    /**
     *  Do a cUrl request
     *
     * //@TODO  cambiar $debug por 'debug mode' para unittest
     *
     */
    private function doRequest($url, $debug = false)
    {
        if ($debug) echo $url.'<hr />';

        $curl = \curl_init();
        \curl_setopt( $curl, CURLOPT_URL, $url );
        \curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
        \curl_setopt( $curl, CURLOPT_USERAGENT, 'Goteo.org Currency Getter');

        $result = \curl_exec( $curl );

        \curl_close( $curl );

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
            $this->source = 'flr';
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
                    $response = self::doRequest($feed_url); //@TODO
                    $file = $response['body'];
                    @$XML=simplexml_load_string($file);
                }

                break;

            case 'flr': // the money converter . com
                // feed request
                $feed_url = str_replace('$BASE$', $base, self::FLR_URL);

                $response = self::doRequest($feed_url); //@TODO
                $file = $response['body'];
                @$XML=simplexml_load_string($file);
                break;
        }

        // verify data
        if (!$XML) {
            // mail de aviso
            $mailHandler = new Mail();
            $mailHandler->to = Config::getMail('fail');
            $mailHandler->subject = 'No coge divisas '.$this->source;
            $mailHandler->content = 'Application\Currency->getData  no obtiene feed desde '.$feed_url.' la respuesta es de '.strlen($response['body']);
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
                        $mailHandler->to = Config::getMail('fail');
                        $mailHandler->subject = 'No coge divisas';
                        $mailHandler->content = 'Application\Currency->getData  no obtiene valor para '.$rate["currency"].' <pre>'.print_r($rate , 1).'</pre>';
                        $mailHandler->html = false;
                        $mailHandler->template = null;
                        $mailHandler->send();
                        unset($mailHandler);

                        continue;
                    }

                    $curId = (string) $rate["currency"];
                    $curVal = (string) $rate["rate"];
                    $rates[$curId] = $curVal;
                }

                break;

            case 'flr': // the money converter . com
                foreach($XML->item as $rate) {

                    if ( empty($rate->exchangeRate) ) {
                        // mail de aviso
                        $mailHandler = new Mail();
                        $mailHandler->to = Config::getMail('fail');
                        $mailHandler->subject = 'No coge divisas';
                        $mailHandler->content = 'Library\Converter->getData flr no obtiene valor para '.$rate->targetCurrency.'</pre>';
                        $mailHandler->html = false;
                        $mailHandler->template = null;
                        $mailHandler->send();
                        unset($mailHandler);

                        continue;
                    }

                    $curId = (string) $rate->targetCurrency;
                    $curVal = (string) $rate->exchangeRate;
                    $rates[$curId] = $curVal;
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
        $base = strtoupper($base);

        // check cache (if not debugging)
        if ($this->cache && !$this->debug) {
            $key = $this->cache->getKey($base, 'rates');
            $rates = $this->cache->retrieve($key);
        }
        if(empty($rates)) {
            $rates = $this->getData($base);
            // sets cache
            if($this->cache) $this->cache->store($key, $rates, $ttl);
        }

        return $rates;

    }


    /**
     * Invalidates the cache
     */
    public function cleanCache() {
        if($this->cache) $this->cache->clean();
    }
}
