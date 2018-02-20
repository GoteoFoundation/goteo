<?php


namespace Goteo\Application\Tests;

use Goteo\Application\Lang;

class LangTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $ob = new Lang();

        $this->assertInstanceOf('\Goteo\Application\Lang', $ob);

        return $ob;
    }

    public function testList() {
        $this->assertInternalType('array', Lang::listAll());
        $all = Lang::listAll('array', false);
        $this->assertInternalType('array', $all);
        return $all;
    }

    /**
     * @depends testList
     * @return [type] [description]
     */
    public function testShortFunctions($all) {
        Lang::setDefault('es');
        $this->assertEquals('es', Lang::getDefault());
        Lang::set('es');
        $current = Lang::current();
        $this->assertEquals('es', $current);
        $this->assertContains($current, array_keys($all));
        $this->assertContains(Lang::getDefault(), array_keys($all));
        $this->assertTrue(Lang::isActive($current));
        $this->assertContains(Lang::getLocale(), Lang::listAll('locale'));
        $this->assertContains(Lang::getName(), Lang::listAll('name'));
        $this->assertContains(Lang::getShort(), Lang::listAll('short'));

        $this->assertTrue(Lang::isPublic($current));
        Lang::setPublic($current, false);
        $this->assertFalse(Lang::isPublic($current));
        $this->assertNotEquals($current, Lang::current(true));
        return $all;
    }

    /**
     * @depends testList
     * @return [type] [description]
     */
    public function testSetters($all) {
        foreach(Lang::listAll('short') as $lang => $info) {
            $this->assertInternalType('string', $info);
        }
        foreach(Lang::listAll('locale') as $lang => $info) {
            $this->assertInternalType('string', $info);
        }
        foreach(Lang::listAll('name') as $lang => $info) {
            $this->assertInternalType('string', $info);
        }
        foreach(Lang::listAll('object') as $lang => $info) {
            $this->assertInternalType('object', $info);
        }
        foreach(Lang::listAll('array') as $lang => $info) {
            $this->assertInternalType('array', $info);
            Lang::set($lang);
            $this->assertEquals($lang, Lang::current());
            $this->assertTrue(Lang::isActive($lang, false));
        }
        Lang::set('es');
        $this->assertEquals('es', Lang::current());
    }
}
