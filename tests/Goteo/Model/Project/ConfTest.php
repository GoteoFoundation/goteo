<?php


namespace Goteo\Model\Project\Tests;

use Goteo\Model\Project\Conf;
use Goteo\TestCase;

class ConfTest extends TestCase {

    public function testInstance(): Conf
    {
        $conf = new Conf();
        $this->assertInstanceOf(Conf::class, $conf);
        return $conf;
    }

    /**
     * @depends testInstance
     */
    public function testOnCreateImpactCalcIsNotActive(Conf $conf): Conf
    {
        $this->assertFalse($conf->isImpactCalcActive());
        return $conf;
    }

    /**
     * @depends testInstance
     */
    public function testValidate(Conf $conf): Conf
    {
        $errors = [];
        $this->assertTrue($conf->validate($errors), implode(',', $errors));
        return $conf;
    }

    /**
     * @depends testValidate
     */
    public function testSave(Conf $conf): Conf
    {
        $conf->project = get_test_project()->id;
        $this->assertTrue($conf->save());
        return $conf;
    }

    /**
     * @depends testSave
     */
    public function testGet(Conf $conf): Conf
    {
        $confDB = Conf::get($conf->project);
        $this->assertObjectEquals($conf, $confDB);

        return $conf;
    }
}
