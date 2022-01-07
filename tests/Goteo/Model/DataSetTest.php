<?php

namespace  Goteo\Model\Tests;

use Goteo\Model\DataSet;
use Goteo\TestCase;

class DataSetTest extends TestCase {


    public function testInstance(): DataSet {
        $ob = new DataSet();
        $this->assertInstanceOf(DataSet::class, $ob);

        return $ob;
    }


}
