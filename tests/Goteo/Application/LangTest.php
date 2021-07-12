<?php


namespace Goteo\Application\Tests;

use Goteo\Application\Lang;

class LangTest extends \PHPUnit\Framework\TestCase {

    public function testInstance(): Lang
    {
        $ob = new Lang();

        $this->assertInstanceOf('\Goteo\Application\Lang', $ob);

        return $ob;
    }

    public function testList(): array
    {
        $this->assertIsArray(Lang::listAll());
        $all = Lang::listAll('array', false);
        $this->assertIsArray($all);
        return $all;
    }

    /**
     * @depends testList
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
     */
    public function testSetters() {
        foreach(Lang::listAll('short') as $info) {
            $this->assertIsString($info);
        }
        foreach(Lang::listAll('locale') as $info) {
            $this->assertIsString($info);
        }
        foreach(Lang::listAll('name') as $info) {
            $this->assertIsString($info);
        }
        foreach(Lang::listAll('object') as $info) {
            $this->assertIsObject($info);
        }
        foreach(Lang::listAll('array') as $lang => $info) {
            $this->assertIsArray($info);
            Lang::set($lang);
            $this->assertEquals($lang, Lang::current());
            $this->assertTrue(Lang::isActive($lang, false));
        }
        Lang::set('es');
        $this->assertEquals('es', Lang::current());
    }
}
