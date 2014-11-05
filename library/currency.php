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

            $this->cache = phpFastCache();

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
        public function getRates($base='EUR', $ttl=86400)
        {
            // check cache
            if ($this->cache->isExisting('RATES.'.$base)) {
                if ($this->debug) die('cached');
                return $this->cache->get('RATES.'.$base);
            }

            // feed request
            $feed_url = 'http://themoneyconverter.com/rss-feed/'.$base.'/rss.xml';
            $response = self::doRequest($feed_url, true); //@TODO
            $file = '<?xml version="1.0" encoding="UTF-8" ?> '.$response['body'];
            @$feed=simplexml_load_string($file);
            if (!$feed) {
                // mail de aviso
                $mailHandler = new Mail();
                $mailHandler->to = \GOTEO_FAIL_MAIL;
                $mailHandler->subject = 'No coge divisas';
                $mailHandler->content = 'Library\Currency->getRates  no obtiene feed desde '.$feed_url.' la respuesta es de '.strlen($response['body']);
                $mailHandler->html = false;
                $mailHandler->template = null;
                $mailHandler->send();
                unset($mailHandler);

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

                if (empty($val) ||empty($name)) {
                    // mail de aviso
                    $mailHandler = new Mail();
                    $mailHandler->to = \GOTEO_FAIL_MAIL;
                    $mailHandler->subject = 'No coge divisas';
                    $mailHandler->content = 'Library\Currency->getRates  no obtiene nombre o valor para alguna divisa <pre>'.print_r($rate , 1).'</pre>';
                    $mailHandler->html = false;
                    $mailHandler->template = null;
                    $mailHandler->send();
                    unset($mailHandler);

                    return false;
                }

                $rates[$tc[0]] = array(
                    'value'=>$val,
                    'name'=>$name,
                    'category'=>(string)$rate->category
                );
            }

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
            $result = $rates[$to]['value'] * $amount;

            return $decimal ? round($result,$decimal) : $result;
        }


    }

}