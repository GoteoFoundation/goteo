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
        //the file is updated daily between 2.15 p.m. and 3.00 p.m. CET
        ECB_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml',
        FLR_URL =  'https://www.floatrates.com/daily/$BASE$.xml',
        TEXT_RequestRatesFailed = 'Unable to fetch the currency rates feed.';

    private $debug = false; // activate on dev phase only
    private $cache = null;
    private $source = 'ecb'; // 'flr' = floatrates.com

    public function __construct() {
        $this->setCache(new Cacher('currency'));
        $this->setReader(new ConverterReader(self::ECB_URL));
    }

    public function setCache($cache) {
        $this->cache = $cache;
        return $this;
    }

    public function getCache() {
        return $this->cache;
    }

    public function setReader($reader) {
        $this->reader = $reader;
        return $this;
    }

    public function getReader() {
        return $this->reader;
    }

    /**
     * Request and parse depending on source
     *
     * @return mixed $rates
     */
    private function getData ($base) {

        // European central bank is just for euro
        $this->getReader()->setUrl(self::ECB_URL);
        if ($base != 'EUR' && $this->source == 'ecb') {
            $this->source = 'flr';
            $this->getReader()->setUrl(str_replace('$BASE$', $base, self::FLR_URL));
        }

        $XML = @simplexml_load_string($this->getReader()->get());

        // verify data
        if (!$XML) {
            // mail de aviso
            $mailHandler = new Mail();
            $mailHandler->to = Config::getMail('fail');
            $mailHandler->subject = 'No coge divisas '.$this->source;
            $mailHandler->content = 'Application\Currency->getData  no obtiene feed desde '.$this->getReader()->getUrl().' la respuesta es de '.strlen($this->getReader()->getResult());
            $mailHandler->html = false;
            $mailHandler->template = null;
            $mailHandler->send();
            unset($mailHandler);

            return null;
        }

        if ($this->debug) {
            echo $this->getReader()->getUrl();
            echo \trace($XML);
            die;
        }

        $rates = array();
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
        if ($this->getCache() && !$this->debug) {
            $key = $this->getCache()->getKey($base, 'rates');
            $rates = $this->getCache()->retrieve($key);
        }
        if(empty($rates)) {
            $rates = $this->getData($base);
            // sets cache
            if($this->getCache()) $this->getCache()->store($key, $rates, $ttl);
        }

        return $rates;

    }


    /**
     * Invalidates the cache
     */
    public function cleanCache() {
        if($this->getCache()) $this->getCache()->clean();
    }
}
