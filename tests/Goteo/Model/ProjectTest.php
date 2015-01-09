<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Project;

class ProjectTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Project();

        $this->assertInstanceOf('\Goteo\Model\Project', $converter);

        return $converter;
    }
}
