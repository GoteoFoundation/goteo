<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Library\Currency,
        Goteo\Model;

    class Currencies {

        public static function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

            //  testing currency feature development
            echo '<h1>testing currency feature development</h1>';

            $currencies = Currency::$currencies;
            echo \trace($currencies);
            echo "<br />";

            $Convert = new Currency();
            $usd = $Convert->getRates('EUR');

            echo \trace($usd);

            die;


        }

    }

}
