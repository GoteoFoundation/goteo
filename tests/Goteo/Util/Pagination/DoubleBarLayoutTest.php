<?php

namespace Goteo\Util\Tests;

use Goteo\Util\Pagination\DoubleBarLayout;

class DoubleBarLayoutTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $ob = new DoubleBarLayout();

        $this->assertInstanceOf('\Goteo\Util\Pagination\DoubleBarLayout', $ob);

        return $ob;

    }
}
