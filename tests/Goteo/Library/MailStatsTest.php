<?php

namespace Goteo\Library\Tests;

use Goteo\Library\MailStats;
use Goteo\Library\Mail;

class MailStatsTest extends \PHPUnit_Framework_TestCase {
    /**
     * Ensures has the correct instances
     */
    public function testInstance() {
        $mail = new MailStats();
        $this->assertInstanceOf('\Goteo\Library\MailStats', $mail);
        return $mail;
    }


}
