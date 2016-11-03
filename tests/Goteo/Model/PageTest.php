<?php


namespace Goteo\Library\Tests;

use Goteo\Model\Page;

class PageTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Page();

        $this->assertInstanceOf('\Goteo\Model\Page', $converter);

        return $converter;
    }
}
