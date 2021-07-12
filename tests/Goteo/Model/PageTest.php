<?php


namespace Goteo\Library\Tests;

use Goteo\Model\Page;

class PageTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Page();

        $this->assertInstanceOf('\Goteo\Model\Page', $converter);

        return $converter;
    }
}
