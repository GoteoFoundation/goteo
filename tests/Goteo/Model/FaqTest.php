<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Faq;

class FaqTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Faq();

        $this->assertInstanceOf('\Goteo\Model\Faq', $converter);

        return $converter;
    }
}
