<?php


namespace Goteo\Model\Project\Tests;

use Goteo\Model\Project\Conf;

class ConfTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Conf();

        $this->assertInstanceOf('\Goteo\Model\Project\Conf', $converter);

        return $converter;
    }
}
