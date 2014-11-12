<?php

namespace Goteo\Tests;

use Goteo\Model,
    Goteo\Library\Mail;

class MailTest extends \PHPUnit_Framework_TestCase {
    /**
     * Ensures has the correct instances
     */
    public function testInstance() {
        $mail = new Mail();
        $this->assertTrue($mail instanceOf Mail);
        $this->assertTrue($mail->mail instanceOf \PHPMailer);
        return $mail;
    }

    /**
     * Test validate function
     * @depends testInstance
     */
    public function testValidate($mail) {

        $mail->subject = "A test subject";
        $mail->content = "A test content";
        $mail->to = 'non-valid-email';
        $this->assertFalse($mail->validate($errors));

        $mail->to = 'ivan@microstudi.net';
        $this->assertTrue($mail->validate());

        return $mail;
    }

    /**
     * TODO: mas tests
     */

}
