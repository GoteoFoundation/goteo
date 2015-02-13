<?php


namespace Goteo\Controller\Admin\Tests;

use Goteo\Controller\Admin\Criteria;

class CriteriaTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new Criteria();

        $this->assertInstanceOf('\Goteo\Controller\Admin\Criteria', $controller);

        return $controller;
    }
}
