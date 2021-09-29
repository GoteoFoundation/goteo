<?php


namespace Goteo\Command\Tests;

use Goteo\Console\Command\DBVerifierCommand;

class DbVerifierCommandTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $controller = new DBVerifierCommand();

        $this->assertInstanceOf('\Goteo\Console\Command\DBVerifierCommand', $controller);

        return $controller;
    }
}
