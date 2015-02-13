<?php


namespace Goteo\Model\User\Tests;

use Goteo\Model\User\Translate;

class TranslateTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Translate();

        $this->assertInstanceOf('\Goteo\Model\User\Translate', $converter);

        return $converter;
    }
}
