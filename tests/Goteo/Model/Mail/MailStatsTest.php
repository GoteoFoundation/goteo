<?php


namespace Goteo\Model\Mail\Tests;

use Goteo\Model\Mail\MailStats;
use Goteo\Model\Template;
use Goteo\Model\Mail;

class MailStatsTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new MailStats();

        $this->assertInstanceOf('\Goteo\Model\Mail\MailStats', $converter);

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
        $errors = [];
        $this->assertTrue($mail->save($errors), print_r($errors, 1));

        $stat = MailStats::incMetric($mail->id, 'test@goteo.org', 'TEST_STAT_METRIC');

        $this->assertInstanceOf('\Goteo\Model\Mail\MailStats', $stat);
        $metric = $stat->getMetric();
        $this->assertInstanceOf('\Goteo\Model\Mail\Metric', $metric);
        $this->assertEquals(1, $stat->counter);

        $stat = MailStats::incMetric($mail->id, 'test@goteo.org', 'TEST_STAT_METRIC');
        $this->assertEquals(2, $stat->counter);

        $this->assertEquals('TEST_STAT_METRIC', $metric->metric);
        return $mail;
    }

    /**
     * @depends testIncMetric
     */
    public function testGetList($mail) {
        $list = MailStats::getFromMailId($mail->id);
        $this->assertCount(1, $list);

        //add second
        $stat = MailStats::incMetric($mail->id, 'test@goteo.org', 'TEST_STAT_METRIC_2');
        $this->assertEquals(1, $stat->counter);

        $list = MailStats::getFromMailId($mail->id);
        $this->assertCount(2, $list);


        //delete email test
        $this->assertTrue($mail->dbDelete());

    }

}
