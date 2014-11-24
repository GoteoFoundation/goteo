<?php

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
     *  Converts and prints depending on the on session
     *
     *  requires a Converter instance
     * @ToDo ( need some way to make this instance persistent, so it shouldn't be created on each request )
     *
     */
    public static function amount_format($amount, $decs = 0) {

        $converter = new Converter(self::$currencies);

        $currency = $_SESSION['currency'];
        $ccy = self::$currencies[$currency];

        if ($currency != self::DEFAULT_CURRENCY) {
            $amount = $converter->convert($amount, self::DEFAULT_CURRENCY, $currency);
        }

        if ($amount === false) {
            return '';
        } else {
            return "{$ccy['html']} ".number_format($amount, $decs, $ccy['dec'], $ccy['thou']);
        }

    }

}
