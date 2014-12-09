<?php


namespace Goteo\Library\Tests;

use \PayPal\Service as PPService;

class PayPalTest extends \PHPUnit_Framework_TestCase {

    // Aunque no es obligatorio que exista este archivo de configuración para compilar grunt
    /*
    public function testConfig() {

        $configFile = PP_CONFIG_PATH . 'sdk_config.ini';
//        echo $configFile;
//        var_dump(is_file($configFile));
        $config = file_get_contents($configFile);
//        var_dump($config);

        $this->contains('acct1.UserName');
    }
    */

    public function testInstance() {

        $service  = new PPService\AdaptivePaymentsService;
        var_dump($service);
        die;

        $this->assertInstanceOf('\Paypal\Service\AdaptivePaymentsService', $service);

        return $service;
    }

}
