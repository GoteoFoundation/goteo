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
* Clase para gestionar las divisas
*/

use Goteo\Application\Session;
use Goteo\Library\Converter;

class Currency {


    const
        DEFAULT_CURRENCY = 'EUR';  // @TODO: este valor debería venir de configuración Yaml

    static public $currencies = array(

        'EUR' => array(
            'id' => 'EUR',
            'name' => 'Euro',
            'html' => '€',
            'thou' => '.',
            'dec'  => ',',
            'active' => 1
        ),

        'USD' => array(
            'id' => 'USD',
            'name' => 'U.S. Dollar',
            'html' => '$',
            'thou' => ',',
            'dec'  => '.',
            'active' => 1
        ),

        'GBP' => array(
            'id' => 'GBP',
            'name' => 'British Pound',
            'html' => '£',
            'thou' => ',',
            'dec'  => '.',
            'active' => 1
        ),


    );

    // Return default currency
    static public function getDefault($method = 'array') {
        return self::get(self::DEFAULT_CURRENCY, $method);
    }

    // Return current session currency
    static public function current($method = 'id') {
        return self::get(Session::get('currency'), $method);
    }

    static public function listAll($method = 'array') {
        if($method === 'array') return static::$currencies;
        return array_column(static::$currencies, $method, 'id');
    }

    // Return a currency from the array of currencies
    static public function get($cur = '', $method = 'array') {
        if(!array_key_exists($cur, self::$currencies)) {
            $cur = self::DEFAULT_CURRENCY;
        }
        $cur = self::$currencies[$cur];
        if(array_key_exists($method, $cur)) return $cur[$method];
        return $cur;
    }

    static public function getAll() {

        $currencies = array();

        $converter = new Converter(); // @FIXME : this instance should be persistent for all the requests of amount_format

        if (!$converter instanceof \Goteo\Library\Converter) {
            return null;
        }

        $rates = $converter->getRates(self::DEFAULT_CURRENCY);

        foreach (self::$currencies as $ccy=>$cur) {

            $cur['rate'] = ($ccy == self::DEFAULT_CURRENCY) ? 1 : $rates[$ccy];

            $currencies[$ccy] = $cur;
        }

        return $currencies;

    }

    /*
     *  Converts and prints depending on the on session
     *
     *  requires a Converter instance
     * @ToDo ( need some way to make this instance persistent, so it shouldn't be created on each request )
     *
     */
    public static function amount_format($amount, $decs = 0, $nosymbol = false, $revert = false) {

        // check odd behaviour
        if (!is_float($amount) && !is_numeric($amount)) {
            return '';
        }

        $default = self::DEFAULT_CURRENCY;
        $converter = new Converter(); // @FIXME : this instance should be persistent for all the requests of amount_format

        if (!$converter instanceof \Goteo\Library\Converter) {
            $currency = strtoupper($default);
        } else {
            $currency = strtoupper(self::current());
        }

        // currency data (htmnl, name, thous/decs)
        $ccy = self::$currencies[$currency];

        if ($currency != $default) {
            $rates = $converter->getRates($default);
            $amount = round(($revert) ? $amount / $rates[$currency] : $amount * $rates[$currency]);
        }

        $symbol= $nosymbol ? "" :$ccy['html']." ";

        if ($amount === false) {
            return '';
        } else {
            return $symbol.number_format($amount, $decs, $ccy['dec'], $ccy['thou']);
        }

    }

    /**
     * [format description]
     * @param  [type] $amount   [description]
     * @param  [type] $currency [description]
     * @return [type]           [description]
     */
    public static function format($amount, $currency = null) {
        return self::get($currency, 'html') . ' ' . $amount;
    }


    /**
     * @return int conversion rate for currency in session
     */
    public static function rate($cur = null) {

        if (empty($cur)) {
            $cur = self::current();
        }

        if($cur == Currency::DEFAULT_CURRENCY)
            return 1;

        $converter = new Converter(); // @FIXME : this instance should be persistent for all the requests of amount_format

        if (!$converter instanceof \Goteo\Library\Converter) {
            return 1;
        }

        $rates = $converter->getRates(self::DEFAULT_CURRENCY);
        return $rates[strtoupper($cur)];
    }

    public static function amount($amount, $cur = null) {
        $rate = self::rate($cur);
        return round($amount * $rate);
    }

    public static function amountInverse($amount, $cur = null) {
        $rate = self::rate($cur);
        return round($amount / $rate);
    }

}
