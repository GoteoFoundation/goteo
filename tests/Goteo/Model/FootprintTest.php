<?php


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Model\Footprint;
use Goteo\Model\SocialCommitment;
use Goteo\Application\Session;


class FootprintTest extends TestCase {
    private static $data = ['name' => 'test', 'description' => 'Footprint test text'];

    public function testInstance() {
        $ob = new Footprint();
        $this->assertInstanceOf('\Goteo\Model\Footprint', $ob);

        return $ob;
    }

    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
    }

    public function testCreate() {
        $ob = new Footprint();
        $errors = [];
        $this->assertFalse($ob->validate($errors), implode("\n", $errors));
        $errors = [];
        $ob = new Footprint(self::$data);
        $this->assertTrue($ob->validate($errors), implode("\n", $errors));
        $this->assertTrue($ob->save($errors), implode("\n", $errors));
        $ob = Footprint::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Footprint', $ob);

        foreach(self::$data as $key => $val) {
            $this->assertEquals($ob->$key, $val);
        }
        return $ob;
    }

    /**
     * @depends testCreate
     */
    public function testIcon($ob) {
        $this->assertInstanceOf('\Goteo\Model\Image', $ob->getIcon());
        $this->assertTrue($ob->getIcon()->isAsset());
        $this->assertStringStartsWith(SRC_URL, $ob->getIcon()->getLink());
        $this->assertStringEndsWith("/img/footprint/square/{$ob->id}.png", $ob->getIcon()->getLink());
        $this->assertFalse($ob->setIcon('testimage.png')->getIcon()->isAsset());
        $this->assertStringEndsWith('testimage.png', $ob->getIcon()->getLink());
    }

    /**
     * @depends testCreate
     */
    public function testDelete($ob) {
        $this->assertTrue($ob->dbDelete());
        return $ob;
    }

    /**
     * @depends testDelete
     */
    public function testNonExisting($ob) {
        $ob = Footprint::get($ob->id);
        $this->assertNull($ob);
    }

}
