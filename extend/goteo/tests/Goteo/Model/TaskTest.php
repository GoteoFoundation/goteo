<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Task;

class TaskTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Task();

        $this->assertInstanceOf('\Goteo\Model\Task', $converter);

        return $converter;
    }
}
