<?php


namespace Goteo\Library\Tests;

use Goteo\Library\Newsletter;

class NewsletterTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Newsletter();

        $this->assertInstanceOf('\Goteo\Library\Newsletter', $converter);

        return $converter;
    }
}
