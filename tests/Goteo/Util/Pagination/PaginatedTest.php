<?php

namespace Goteo\Util\Tests;

use Goteo\Util\Pagination\Paginated;

class PaginatedTest extends \PHPUnit_Framework_TestCase {

    protected $page;

    //read config
    public function setUp() {

        $this->page = new \stdClass();

        for($i=0; $i<25; $i++) {
            $this->page->{"page$i"} = "Content $i";
        }

    }

    public function testInstance() {

        $ob = new Paginated($this->page);

        $this->assertInstanceOf('\Goteo\Util\Pagination\Paginated', $ob);

        return $ob;

    }

    /**
     * @depends testInstance
     */
    public function testRs($ob) {
        $this->assertCount(25, $ob->getRs());

        return $ob;
    }

    public function testPage() {
        $ob = new Paginated($this->page, 10);
        $this->assertEquals(10, $ob->getPageSize());
        $this->assertEquals(3, $ob->fetchNumberPages());

        $ob = new Paginated($this->page, 24);
        $this->assertEquals(24, $ob->getPageSize());
        $this->assertEquals(2, $ob->fetchNumberPages());

        $ob = new Paginated($this->page, 25);
        $this->assertEquals(25, $ob->getPageSize());
        $this->assertEquals(1, $ob->fetchNumberPages());

        $ob = new Paginated($this->page, 26);
        $this->assertEquals(26, $ob->getPageSize());
        $this->assertEquals(1, $ob->fetchNumberPages());

        return $ob;
    }

    //More...
}
