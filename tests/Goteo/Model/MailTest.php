<?php

namespace Goteo\Model\Tests;

use Goteo\Model\Template;
use Goteo\Model\Mail;
use Goteo\Application\Config;

class MailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures has the correct instances
     */
    public function testInstance(): Mail
    {
        $mail = new Mail();
        $this->assertInstanceOf('\Goteo\Model\Mail', $mail);
        $this->assertInstanceOf('\PHPMailer', $mail->mail);
        return $mail;
    }

    /**
     * Test validate function
     * @depends testInstance
     */
    public function testValidate(Mail $mail): Mail
    {
        $mail->subject = "A test subject";
        $mail->content = "A test content";
        $mail->to = 'non-valid-email';
        $this->assertFalse($mail->validate());

        $mail->to = 'test@goteo.org';
        $errors = [];
        $this->assertTrue($mail->validate($errors), print_r($errors, 1));

        return $mail;
    }

    /**
     * @depends testValidate
     */
    public function testMessage(Mail $mail)
    {
        $mailer = $mail->buildMessage();
        $this->assertInstanceOf('\PHPMailer', $mailer);

        $this->assertEquals('test@goteo.org', $mailer->getToAddresses()[0][0]);

        $this->assertContains('/logo-fg-white.png" alt="Fundación Goteo"', $mailer->Body);
        $this->assertContains('<title>' . $mail->subject . '</title>', $mailer->Body);
    }

    private function encode($mail, $url='')
    {
        return mybase64_encode(md5(Config::get('secret') . '-' . $mail->to . '-' . $mail->id. '-' . $url) . '¬' . $mail->to  . '¬' . $mail->id . '¬' . $url);
    }

    /**
     * This test is likely to fail if there's no DB
     *
     * @depends testValidate
     */
    public function testNewsletterMessage(Mail $mail)
    {
        $mail->template = Template::NEWSLETTER;
        $mailer = $mail->buildMessage();
        $leave_url = Config::getMainUrl() . '/user/unsubscribe/' . $mail->getToken();
        $unsubscribe_url = Config::getMainUrl() . '/mail/url/' . $this->encode($mail, $leave_url);
        $this->assertContains($unsubscribe_url, $mailer->Body);
        $this->assertContains('/goteo-white.png"', $mailer->Body);
        $this->assertContains($mail->content, $mailer->Body);
    }

    public function testToken()
    {
        $mail = new Mail();
        $mail->to = 'test@goteo.org';
        $mail->id = 12345;
        $token = $mail->getToken();
        $decoded = Mail::decodeToken($token);
        $this->assertEquals('test@goteo.org', $decoded[0]);
        $this->assertEquals(12345, $decoded[1]);

        $this->assertTrue(empty(Mail::decodeToken('invalid¬token')));
    }

    public function testCreateText()
    {
        $mail = Mail::createFromText('test@goteo.org', 'Test', 'Subject', "Body\nsecond line");
        $mailer = $mail->buildMessage();
        $this->assertInstanceOf('\PHPMailer', $mailer);
        $this->assertEquals('test@goteo.org', $mailer->getToAddresses()[0][0]);
        $this->assertEquals('Test', $mailer->getToAddresses()[0][1]);
        $this->assertContains('Subject', $mailer->Subject);
        $this->assertEmpty($mailer->isHTML());
        $this->assertContains("Body\nsecond line", $mailer->Body);
        $this->assertContains($mail->getToken(), $mailer->Body);
    }

    public function testCreateHtml()
    {
        $mail = Mail::createFromHtml('test@goteo.org', 'Test', 'Subject', "Body<br>second line");
        $mailer = $mail->buildMessage();

        $this->assertInstanceOf('\PHPMailer', $mailer);
        $this->assertEquals('test@goteo.org', $mailer->getToAddresses()[0][0]);
        $this->assertEquals('Test', $mailer->getToAddresses()[0][1]);
        $this->assertContains('Subject', $mailer->Subject);
        $this->assertEmpty($mailer->isHTML());
        $this->assertContains("Body<br>second line", $mailer->Body);
        $this->assertContains("Body\nsecond line", $mailer->AltBody);
        $this->assertContains($mail->getToken(), $mailer->Body);
    }

    public function testCreateTemplate(): Mail
    {
        $tpl = Template::get(Template::NEWSLETTER);
        $mail = Mail::createFromTemplate('test@goteo.org', 'Test', Template::NEWSLETTER);
        $mailer = $mail->buildMessage();

        $this->assertInstanceOf('\PHPMailer', $mailer);
        $this->assertEquals('test@goteo.org', $mailer->getToAddresses()[0][0]);
        $this->assertEquals('Test', $mailer->getToAddresses()[0][1]);
        $this->assertContains($tpl->title, $mailer->Subject);
        $this->assertEmpty($mailer->isHTML());
        $this->assertContains($mail->getToken(), $mailer->Body);

        return $mail;
    }

    /**
     * @depends testCreateTemplate
     */
    public function testSaveDB(Mail $mail)
    {
        $errors = [];
        $this->assertEmpty($mail->id);
        $this->assertTrue($mail->save($errors), implode("\n", $errors));
        $this->assertNotEmpty($mail->id);
        $this->assertTrue($mail->dbDelete());
    }

}
