<?php


namespace Goteo\Model\Call\Tests;

use Goteo\Model\Call\Project;

class ProjectTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Project();

        $this->assertInstanceOf('\Goteo\Model\Call\Project', $converter);

        return $converter;
    }
}
