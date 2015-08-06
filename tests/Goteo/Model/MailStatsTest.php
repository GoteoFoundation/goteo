<?php


namespace Goteo\Model\Tests;

use Goteo\Model\MailStats;
use Goteo\Model\Template;
use Goteo\Model\Mail;

class MailStatsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new MailStats();

        $this->assertInstanceOf('\Goteo\Model\MailStats', $converter);

        return $converter;
    }

    public function testValidate() {
        $metric = new MailStats();
        $this->assertFalse($metric->validate());
        $metric->mail_id = 1;
        $this->assertFalse($metric->validate());
        $metric->metric_id = 1;
        $this->assertFalse($metric->validate());
        $metric->email = 'test@test.com';
        $this->assertTrue($metric->validate());
    }


    public function testIncMetric() {

        try {
            MailStats::incMetric(-1, 'test@goteo.org', 'TEST_STAT_METRIC');
            $this->fail('No exception thrown!');
        }
        catch(\Goteo\Application\Exception\ModelException $e) {
            $this->assertNotEmpty($e->getMessage());
        }
        //Create test mail
        $tpl = Template::get(Template::NEWSLETTER);
        $mail = Mail::createFromTemplate('test@goteo.org', 'Test', Template::NEWSLETTER);
        $mail->buildMessage();
        $this->assertTrue($mail->save($errors), implode("\n", $errors));

        $stat = MailStats::incMetric($mail->id, 'test@goteo.org', 'TEST_STAT_METRIC');

        $this->assertInstanceOf('\Goteo\Model\MailStats', $stat);
        $metric = $stat->getMetric();
        $this->assertInstanceOf('\Goteo\Model\Metric', $metric);
        $this->assertEquals(1, $stat->counter);

        $stat = MailStats::incMetric($mail->id, 'test@goteo.org', 'TEST_STAT_METRIC');
        $this->assertEquals(2, $stat->counter);

        $this->assertEquals('TEST_STAT_METRIC', $metric->metric);

        //delete email test
        $this->assertTrue($mail->dbDelete());
    }
}
