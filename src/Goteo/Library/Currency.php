<?php

namespace Goteo\Library;

/*
* Clase para gestionar las divisas
*/

use Goteo\Library\Mail,
    Goteo\Library\Cacher;

class Currency {


    const
        DEFAULT_CURRENCY = 'EUR',
        ECB_URL = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml',
        TMC_URL =  'http://themoneyconverter.com/rss-feed/$BASE$/rss.xml',
        TEXT_RequestRatesFailed = 'Unable to fetch the currency rates feed.',
        TEXT_AmountMustBeNumeric = 'The given amount to convert must be numeric.',
        TEXT_LocaleNotAvailable = 'The given locale `%s` is not installed on this system.';

    private $debug;
    private $cache = null;
    private $source = 'ecb'; // 'tmc' = themoneyconverter.com

    static public $currencies = array(

        'EUR' => array(
            'name' => 'Euro',
            'html' => '&euro;',
            'thou' => '.',
            'dec'  => ',',
            'active' => 1
        ),

        'USD' => array(
            'name' => 'U.S. Dollar',
            'html' => '&dollar;',
            'thou' => ',',
            'dec'  => '.',
            'active' => 1
        ),

        'GBP' => array(
            'name' => 'British Pound',
            'html' => '&pound;',
            'thou' => ',',
            'dec'  => '.',
            'active' => 1
        ),


    );

    public function __construct($debug = false) {
        $this->debug = $debug;
        //TODO: mejor pasar la dependencia por el constructor?
        $this->cache = new Cacher('currency');
    }


    /*
     * Establece la divisa de visualización de la web
     *
     * $_GET['currency'] : parametro para cambio de divisa
     * $_SESSION['currency'] : variable de sesión para mantener la divisa
     * \CURRENCY : constante divisa de visualización para usar en el código
     * $_COOKIE['currency'] : cookie para recordar la última divisa que seleccionó el usuario
     */
    static public function set($force = null)
    {
        // @TODO : cambiar por configuración
        $default_currency = self::DEFAULT_CURRENCY;

        // si lo estamos forzando
        if (isset($force)) {
            $newCur = strtoupper($force);

        } elseif (isset($_GET['currency']) && !empty($_GET['currency'])) {

            $newCur = strtoupper($_GET['currency']);

            if (!isset(self::$currencies[$newCur])) $newCur = $default_currency;

            setcookie("currency", $newCur, time() + 3600 * 24 * 365);

        } elseif (empty($_SESSION['currency'])) {
            //primero miramos si tiene cookie
            $newCur = (isset($_COOKIE['currency'])) ? $_COOKIE['currency'] : $default_currency;
        } else {
            $newCur = $_SESSION['currency'];
        }

        // return whatever to be set in the constant
        return $newCur;
    }

    /*
     *  Converts and prints depending on the \CURRENCY
     */
    public static function amount_format($amount, $decs = 0) {

        $ccy = self::$currencies[\CURRENCY];

        return "{$ccy['html']} ".number_format($amount, $decs, $ccy['dec'], $ccy['thou']);
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
                        $mailHandler->content = 'Library\Currency->getData  no obtiene valor para '.$tc[0].' <pre>'.print_r($rate , 1).'</pre>';
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
    public function convert($amount, $from, $to, $decimal = 2)
    {
        if (!is_float($amount) && !is_numeric($amount)) {
            trigger_error(self::TEXT_AmountMustBeNumeric);
            return false;
        }
        if($from == $to) return $amount;

        $rates = $this->getRates($from);
        $result = $rates[$to] * $amount;

        return $decimal ? round($result,$decimal) : $result;
    }

    /**
     * Invalidates the cache
     */
    public function cleanCache() {
        if($this->cache) $this->cache->clean();
    }
}
