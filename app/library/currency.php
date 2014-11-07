<?php

namespace Goteo\Library {

/*
 * Clase para gestionar las monedas
 */
    use \Goteo\Library\Mail;

    class Currency {


        const
            TEXT_RequestRatesFailed = 'Unable to fetch the currency rates feed.',
            TEXT_AmountMustBeNumeric = 'The given amount to convert must be numeric.',
            TEXT_LocaleNotAvailable = 'The given locale `%s` is not installed on this system.';

        private $debug;
        private $cache;
        private $source = 'ecb'; // 'tmc' = themoneyconverter.com

        static public $currencies = array(

            'EUR' => array(
                'html' => '&euro;',
                'name' => 'Euro'
            ),

            'USD' => array(
                'html' => '&dollar;',
                'name' => 'U.S. Dollar'
            ),

            'GBP' => array(
                'html' => '&pound;',
                'name' => 'British Pound'
            ),


        );

        public function __construct($debug = false) {
            require_once PHPFASTCACHE_CLASS;

            if(SQL_CACHE_DRIVER == 'memcache') {
                \phpFastCache::setup('storage','memcache');
                \phpFastCache::setup('server',array(array(
                    defined('SQL_CACHE_SERVER') ? SQL_CACHE_SERVER : '127.0.0.1',
                    defined('SQL_CACHE_PORT') ? SQL_CACHE_PORT : 11211,
                    1)
                ));
            }
            else {
                \phpFastCache::setup('storage','files');
                \phpFastCache::setup('path', GOTEO_DATA_PATH);
            }

            $this->cache = \phpFastCache();

            $this->debug = $debug;
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
                    $feed_url = "http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml";

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
                    $feed_url = 'http://themoneyconverter.com/rss-feed/'.$base.'/rss.xml';
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
            if (!$this->debug && $this->cache->isExisting('RATES.'.$base)) {
                return $this->cache->get('RATES.'.$base);
            }

            $rates = $this->getData($base);

            // set cache
            $this->cache->set('RATES.'.$base,$rates,$ttl);
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


    }

}