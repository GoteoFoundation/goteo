<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Invest;

class InvestTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new Invest();

        $this->assertInstanceOf('\Goteo\Model\Invest', $converter);

        return $converter;
    }
}
