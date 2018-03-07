<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application;


/*
* Clase para gestionar las divisas
*/

use Goteo\Application\App;
use Goteo\Application\Session;
use Goteo\Library\Converter;

class Currency {

    protected static $default_currency = 'EUR';
    protected static $converter;

    static public $currencies = array(

        'EUR' => array(
            'id' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'html' => '€', // legacy var
            'thousands' => '.',
            'thou' => '.', // Legacy var
            'decimal'  => ',',
            'dec'  => ',', // legacy var
            'active' => true
        )

    );

    static public function setConverter(Converter $converter) {
        static::$converter = $converter;
    }

    static public function getConverter() {
        if( !static::$converter instanceOf Converter ) {
            static::setConverter(App::getService('app.currency.converter'));
        }
        return static::$converter;
    }

    // Sets the default currency
    static public function setDefault($id) {
        if(!isset(static::$currencies[$id])) throw new \RuntimeException("Currency [$id] not registered!");
        static::$default_currency = $id;
    }

    // Return default currency
    static public function getDefault($method = 'array') {
        return static::get(static::$default_currency, $method);
    }

    // Return current session currency
    static public function current($method = 'id') {
        return static::get(Session::get('currency'), $method);
    }

    static public function addCurrency(array $currency) {
        if(!isset($currency['id'])) throw new \RuntimeException("Currency array shoud contain [id] var");
        $currency['id'] = strtoupper($currency['id']);
        // Compatibilize legacy keys
        if(!isset($currency['html'])) $currency['html'] = $currency['symbol'];
        if(!isset($currency['thou'])) $currency['thou'] = $currency['thousands'];
        if(!isset($currency['dec'])) $currency['dec'] = $currency['decimal'];
        static::$currencies[$currency['id']] = $currency;
        return true;
    }

    static public function setCurrenciesAvailable(array $currencies) {
        static::$currencies = [];
        foreach($currencies as $key => $currency) {
            if(!isset($currency['id'])) $currency['id'] = $key;
            static::addCurrency($currency);
        }
    }

    static public function listAll($method = 'array') {
        if($method === 'array') return static::$currencies;
        return array_column(static::$currencies, $method, 'id');
    }

    // Return a currency from the array of currencies
    static public function get($cur = '', $method = 'array') {
        if(!array_key_exists($cur, static::$currencies)) {
            $cur = static::$default_currency;
        }
        $cur = static::$currencies[$cur];
        if(array_key_exists($method, $cur)) return $cur[$method];
        return $cur;
    }

    static public function getAll() {

        $currencies = array();

        $converter = static::getConverter();

        $rates = $converter->getRates(static::$default_currency);

        foreach (static::$currencies as $ccy => $cur) {

            $cur['rate'] = ($ccy == static::$default_currency) ? 1 : $rates[$ccy];

            $currencies[$ccy] = $cur;
        }

        return $currencies;

    }

    /*
     *  Converts and prints depending on the on session
     *
     *  requires a Converter instance
     */
    static public function amountFormat($amount, $decs = 0, $nosymbol = false, $revert = false, $format = true) {

        // check odd behaviour
        if (!is_float($amount) && !is_numeric($amount)) {
            return '';
        }

        $default = static::$default_currency;
        $converter = static::getConverter();
        $currency = static::current();

        // currency data (htmnl, name, thous/decs)
        $ccy = static::$currencies[$currency];

        if ($currency != $default) {
            $rates = $converter->getRates($default);
            $amount = round($revert ? $amount / $rates[$currency] : $amount * $rates[$currency]);
        }

        $symbol = $nosymbol ? "" : $ccy['symbol']." ";

        if ($amount === false) {
            $ret = $format ? '' : 0;
        } else {
            $ret = $format ? $symbol . number_format($amount, $decs, $ccy['decimal'], $ccy['thousands']) : round($amount, $decs);
        }
        return $ret;
    }

    /**
     * [format description]
     * @param  [type] $amount   [description]
     * @param  [type] $currency [description]
     * @return [type]           [description]
     */
    public static function format($amount, $currency = null) {
        return static::get($currency, 'symbol') . ' ' . $amount;
    }


    /**
     * @return int conversion rate for currency in session
     */
    public static function rate($cur = null) {

        if (empty($cur)) {
            $cur = static::current();
        }

        if($cur == static::$default_currency)
            return 1;

        $converter = static::getConverter();

        $rates = $converter->getRates(static::$default_currency);
        return $rates[strtoupper($cur)];
    }

    public static function amount($amount, $cur = null) {
        $rate = static::rate($cur);
        return round($amount * $rate);
    }

    public static function amountInverse($amount, $cur = null) {
        $rate = static::rate($cur);
        return round($amount / $rate);
    }

}
