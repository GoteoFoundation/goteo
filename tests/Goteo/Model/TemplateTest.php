<?php


namespace Goteo\Model\Tests;

use Goteo\Model\Template;

class TemplateTest extends \PHPUnit_Framework_TestCase {

    public function testInstance() {

        $converter = new Template();

        $this->assertInstanceOf('\Goteo\Model\Template', $converter);

        return $converter;
    }


    public function testMessage() {
        foreach([Template::MESSAGE_CONTACT, Template::NEWSLETTER] as $id) {
            $template = Template::get($id);
            $this->assertEquals($id, $template->id);
        }
    }
}
