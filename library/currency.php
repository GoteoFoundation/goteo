<?php

namespace Goteo\Library {

/*
 * Clase para gestionar las monedas
 */

    class Currency {

        const
            TEXT_RequestRatesFailed = 'Unable to fetch the currency rates feed.',
            TEXT_AmountMustBeNumeric = 'The given amount to convert must be numeric.',
            TEXT_LocaleNotAvailable = 'The given locale `%s` is not installed on this system.';

        private $debug = false;

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
         * fetch currency rates based on given base currency
         * @param string $base currency ISO code
         * @param int $ttl caching time in seconds
         * @return array
         */
        public function getRates($base='USD', $ttl=86400)
        {
            // check cache
            //@TODO
            /*
            if ($this->f3->exists('RATES.'.$base))
                return $this->f3->get('RATES.'.$base);
            */

            // feed request
            $feed_url = 'http://themoneyconverter.com/rss-feed/'.$base.'/rss.xml';
            $response = self::doRequest($feed_url); //@TODO
            $file = '<?xml version="1.0" encoding="UTF-8" ?> '.$response['body'];
            @$feed=simplexml_load_string($file);
            if (!$feed) {
                die (self::TEXT_RequestRatesFailed);
                return false;
            }

            if ($this->debug) {
                echo \trace($feed);
                die;
            }

            // parse response
            $rates = array();
            foreach($feed->channel->item as $rate) {
                $tc = explode('/',$rate->title);
                $ex = explode(' = ',$rate->description);
                list($val,$name) = explode(' ',$ex[1],2);
                $rates[$tc[0]] = array(
                    'value'=>$val,
                    'name'=>$name,
                    'category'=>(string)$rate->category
                );
            }

            // set cache
            // return $this->f3->set('RATES.'.$base,$rates,$ttl); //@TODO
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
            $result = $rates[$to]['value'] * $amount;

            return $decimal ? round($result,$decimal) : $result;
        }


    }

}