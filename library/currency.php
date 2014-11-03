<?php

namespace Goteo\Library {

/*
 * Clase para gestionar las monedas
 */

    class Currency {

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


    }

}