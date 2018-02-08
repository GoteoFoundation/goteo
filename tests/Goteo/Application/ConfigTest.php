<?php


namespace Goteo\Application\Tests;

use Goteo\Application\Config;
use Goteo\Application\Lang;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class ConfigTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $ob = new Config();

        $this->assertInstanceOf('\Goteo\Application\Config', $ob);

        return $ob;
    }

    /**
     * Validate YAML lang files
     */
    public function testYamlLangFiles() {
        try {
            foreach (Lang::listAll('name', false) as $lang => $name) {
                foreach (Config::$trans_groups as $group) {
                    $file = GOTEO_PATH . 'Resources/translations/' . $lang . '/' . $group . '.yml';
                    if(is_file($file)) {
                        $yaml = Yaml::parse(file_get_contents($file));
                        if($yaml) $this->assertInternalType('array', $yaml, $file);
                    }
                }
            }
        } catch(ParseException $e) {
            $this->fail("YAML parse error in [$file]\n" . $e->getMessage());
        }
    }

}
