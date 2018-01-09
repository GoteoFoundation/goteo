<?php


namespace Goteo\Library\Tests;

use Goteo\Application\Message;

class MessageTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Message('info', 'test message');

        $this->assertInstanceOf('\Goteo\Application\Message', $converter);
        //clear the message
        Message::clear();
        $this->assertCount(0, Message::getAll());
    }

    public function testAdder() {
        Message::info('Info message 1');
        Message::info('Info message 2');
        $this->assertCount(2, Message::getAll());
        Message::error('Error message 1');
        Message::error('Error message 2');
        Message::error('Error message 2'); //repeated message
        Message::error('Error message 3');
        $this->assertCount(5, Message::getAll());
    }

    public function testGetter() {
        $msgs = Message::getMessages(false);
        $this->assertCount(2, $msgs);
        $errors = Message::getErrors(false);
        $this->assertCount(3, $errors);
    }

    public function testDelete() {
        Message::error('Error message 4');
        $this->assertCount(6, Message::getAll());
        $this->assertTrue(end(Message::getAll())->del());
        $this->assertCount(5, Message::getAll());
    }

    public function testGetterAutoexpire() {
        $msgs = Message::getMessages();
        $this->assertCount(2, $msgs);
        $this->assertCount(0, Message::getMessages());
        $this->assertCount(3, Message::getAll());
        $errors = Message::getErrors();
        $this->assertCount(3, $errors);
        $this->assertCount(0, Message::getErrors());
        $this->assertCount(0, Message::getAll());
    }
}
