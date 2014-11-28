<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Page;

class PageTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Page();

        $this->assertInstanceOf('\Goteo\Library\Page', $converter);

        return $converter;
    }
}
