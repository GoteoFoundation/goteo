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

    public function testShallowEnvSubstitution() {
        $expected = "a nice value";
        Config::set("test.shallow_sub_env_var", "%env(SOME_NICE_ENV_VAR)%");
        $this->assertEmpty($readParameterValue);
        putenv("SOME_NICE_ENV_VAR=$expected");

        $readParameterValue = Config::get("test.shallow_sub_env_var");
        $this->assertEquals($expected, $readParameterValue);
    }

    public function testDeepEnvSubstitution() {
        $expected = "a nice value";
        Config::set("test.deep.sub.env.var", "%env(ANOTHER_NICE_ENV_VAR)%");
        $this->assertEmpty($readParameterValue);
        putenv("ANOTHER_NICE_ENV_VAR=$expected");

        $readParameterValue = Config::get("test.deep.sub.env.var");
        $this->assertEquals($expected, $readParameterValue);

        $readParameterParent = Config::get("test.deep.sub.env");
        $this->assertEquals($expected, $readParameterParent["var"]);

        $readParameterAntecessor = Config::get("test");
        $this->assertEquals($expected, $readParameterAntecessor["deep"]["sub"]["env"]["var"]);
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
