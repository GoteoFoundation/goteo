<?php


namespace Goteo\Application\Tests;

use Goteo\Application\Config;
use Goteo\Application\Lang;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

class ConfigTest extends \PHPUnit\Framework\TestCase {

    public function testInstance(): Config
    {
        $ob = new Config();

        $this->assertInstanceOf('\Goteo\Application\Config', $ob);

        return $ob;
    }

    public function testValidateYamlLangFiles() {
        try {
            foreach (Lang::listAll('name', false) as $lang => $name) {
                foreach (Config::$trans_groups as $group) {
                    $file = GOTEO_PATH . 'Resources/translations/' . $lang . '/' . $group . '.yml';
                    if(is_file($file)) {
                        $yaml = Yaml::parse(file_get_contents($file));
                        if($yaml) $this->assertIsArray($yaml, $file);
                    }
                }
            }
        } catch(ParseException $e) {
            $this->fail("YAML parse error in [$file]\n" . $e->getMessage());
        }
    }

    public function testYamlLangFilesWithEnvParameter() {
        $expectedDatabasePortEnv = 33061;
        putenv("DATABASE_PORT=$expectedDatabasePortEnv");

        $readParameterValue = Config::get("db.port_env");

        $this->assertEquals($expectedDatabasePortEnv, $readParameterValue);
    }

    public function testYamlLangFilesWithEnvParameterInArrays() {
        $expectedDatabasePortEnv = 33061;
        putenv("DATABASE_PORT=$expectedDatabasePortEnv");

        $readParameterValue = Config::get("db.array_env");

        $this->assertEquals([$expectedDatabasePortEnv], $readParameterValue);
    }
}
