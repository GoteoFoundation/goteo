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

            switch ($action) {
                case 'list':

                    echo \trace(Currency::$currencies);

                    break;

                case 'test':
                    $Lib = new Currency();

                    // get rates test
                    $usd = $Lib->getRates('EUR');
                    echo \trace($usd);

                    break;

                case 'convert':
                    $Lib = new Currency();

                    $amount = (empty($id)) ? 1 : $id;

                    foreach (Currency::$currencies as $curId=>$curData) {
                        echo "{$amount} EUR = ".$Lib->convert($amount, 'EUR', $curId)." $curId<br />";
                    }

                    break;
            }




            die;


        }

    }

}
