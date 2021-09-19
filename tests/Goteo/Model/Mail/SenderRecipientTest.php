<?php


namespace Goteo\Model\Mail\Tests;

use Goteo\Model\Mail\SenderRecipient;

class SenderRecipientTest extends \PHPUnit\Framework\TestCase {

    public function testInstance() {

        $converter = new SenderRecipient();

        $this->assertInstanceOf('\Goteo\Model\Mail\SenderRecipient', $converter);

        return $converter;
    }
}
