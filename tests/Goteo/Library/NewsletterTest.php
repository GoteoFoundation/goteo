<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Newsletter;

class NewsletterTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Newsletter();

        $this->assertInstanceOf('\Goteo\Library\Newsletter', $converter);

        return $converter;
    }
}
