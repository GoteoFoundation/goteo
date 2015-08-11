<?php


namespace Goteo\Model\Mail\Tests;

use Goteo\Model\Mail\Metric;

class MetricTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Metric();

        $this->assertInstanceOf('\Goteo\Model\Mail\Metric', $converter);

        return $converter;
    }

    public function testValidate() {
        $metric = new Metric();
        $this->assertFalse($metric->validate());
    }

    public function testErrorCreate() {
        try {
            $metric = Metric::getMetric('');
            $this->fail('Non exception thrown');
        } catch(\Goteo\Application\Exception\ModelException $e) {
            $this->assertNotEmpty($e->getMessage());
        }
    }

    public function testCreate() {
        $metric = Metric::getMetric('TEST_METRIC');
        $this->assertInstanceOf('\Goteo\Model\Mail\Metric', $metric);
        $this->assertEquals('TEST_METRIC', $metric->metric);
        $this->assertNotEmpty($metric->id);
        $this->assertGreaterThan(0, $metric->id);
        $metric->desc = 'Test desc';
        $this->assertTrue($metric->save());
        $new_metric = Metric::get($metric->id);
        $this->assertEquals('TEST_METRIC', $new_metric->metric);
        $this->assertEquals('Test desc', $new_metric->desc);
        return $new_metric;
    }

    /**
     * @depends testCreate
     */
    public function testDelete($metric) {
        $metric->dbDelete();
        $metric = Metric::get($metric->id);
        $this->assertFalse($metric);
    }
}
