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
        $this->assertInstanceOf('\Goteo\Library\Mail', $mail);
        $this->assertInstanceOf('\PHPMailer', $mail->mail);
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
     * @depends testValidate
     */
    public function testMessage($mail) {
        $mailer = $mail->buildMessage();
        $this->assertInstanceOf('\PHPMailer', $mailer);

        $this->assertEquals('ivan@microstudi.net', $mailer->getToAddresses()[0][0]);

        $this->assertContains('<img src="cid:logo" alt="Goteo"/>', $mailer->Body);
        $this->assertContains('<title>Goteo Mailer</title>', $mailer->Body);
    }

    /**
     * @depends testValidate
     */
    public function testNewsletterMessage($mail) {
        $mail->template = 33;
        $mailer = $mail->buildMessage();
        $this->assertContains('/user/unsuscribe/', $mailer->Body);
        $this->assertContains('<title>Goteo Newsletter</title>', $mailer->Body);
    }

    /**
     * @depends testValidate
     */
    public function testNodeMessage($mail) {
        $mail->template = 0;
        //cosa rara que hay en el mailer...
        $_SESSION['admin_node'] = 'testnode';
        $mail->node = 'testnode';
        $mailer = $mail->buildMessage();
        $this->assertContains('<title>Goteo Nodo Test.goteo.org Mailer</title>', $mailer->Body);
    }
}
