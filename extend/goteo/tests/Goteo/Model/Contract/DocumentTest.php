<?php


namespace Goteo\Model\Contract\Tests;

use Goteo\Model\Contract\Document;

class DocumentTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Document();

        $this->assertInstanceOf('\Goteo\Model\Contract\Document', $converter);

        return $converter;
    }
}
