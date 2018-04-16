<?php


namespace Goteo\Command\Tests;

use Goteo\Console\Command\TranslationsCommand;

class TranslationsCommandTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $controller = new TranslationsCommand();

        $this->assertInstanceOf('\Goteo\Console\Command\TranslationsCommand', $controller);

        return $controller;
    }
}
