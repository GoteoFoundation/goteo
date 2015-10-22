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

use Goteo\Library\Converter;

class Currency {


    const
        DEFAULT_CURRENCY = 'EUR';  // @TODO: este valor debería venir de configuración Yaml

    static public $currencies = array(

        'EUR' => array(
            'id' => 'EUR',
            'name' => 'Euro',
            'html' => '&euro;',
            'thou' => '.',
            'dec'  => ',',
            'active' => 1
        ),

        'USD' => array(
            'id' => 'USD',
            'name' => 'U.S. Dollar',
            'html' => '&dollar;',
            'thou' => ',',
            'dec'  => '.',
            'active' => 1
        ),

        'GBP' => array(
            'id' => 'GBP',
            'name' => 'British Pound',
            'html' => '&pound;',
            'thou' => ',',
            'dec'  => '.',
            'active' => 1
        ),


    );

    // Return default currency
    static public function getDefault() {
        return self::$currencies[self::DEFAULT_CURRENCY];
    }

    static public function get() {
        $cur = $_SESSION['currency'];
        if(empty($cur)) $cur = self::$currencies[self::DEFAULT_CURRENCY]['id'];
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
     * Establece la divisa de visualización de la web
     *
     * $_GET['currency'] : parametro para cambio de divisa
     * $_SESSION['currency'] : variable de sesión para mantener la divisa
     * $_COOKIE['currency'] : cookie para recordar la última divisa que seleccionó el usuario
     */
    static public function set($force = null)
    {
        // @TODO : cambiar por configuración
        $default_currency = self::DEFAULT_CURRENCY;

        // si lo estamos forzando
        if (isset($force)) {
            $newCur = strtoupper($force);
            if (!isset(self::$currencies[$newCur])) $newCur = $default_currency;

        } elseif (isset($_GET['currency']) && !empty($_GET['currency'])) {

            $newCur = strtoupper($_GET['currency']);
            if (!isset(self::$currencies[$newCur])) $newCur = $default_currency;

            setcookie("currency", $newCur, time() + 3600 * 24 * 365);

        } elseif (empty($_SESSION['currency'])) {

            // aquí debería ser la divisa preferida por el usuario
            // pero a la primera carga ya habrá metido la default en sessión
            // ponemos la divisa preferida por el usuario cuando este hace login
            // ver model/user::login()

            // luego miramos si tiene cookie
            $newCur = (isset($_COOKIE['currency'])) ? $_COOKIE['currency'] : $default_currency;
        } else {
            $newCur = $_SESSION['currency'];
        }

        // return whatever to be set in the constant
        return strtoupper($newCur)  ;
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
            $currency = strtoupper($_SESSION['currency']);
        }

        // currency data (htmnl, name, thous/decs)
        $ccy = self::$currencies[$currency];

        if ($currency != $default) {
            $rates = $converter->getRates($default);
            $amount = ($revert) ? $amount / $rates[$currency] : $amount * $rates[$currency];
        }

        $symbol= $nosymbol ? "" :$ccy['html']." ";

        if ($amount === false) {
            return '';
        } else {
            return $symbol.number_format($amount, $decs, $ccy['dec'], $ccy['thou']);
        }

    }


    /**
     * @return int conversion rate for currency in session
     */
    public static function rate($cur = null) {

        if (empty($cur)) {
            $cur = $_SESSION['currency'];
        }

        if($_SESSION['currency'] == Currency::DEFAULT_CURRENCY)
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
        $amount =  round($amount / $rate);
        return number_format($amount, 0, '', '');
    }

}
