<?php


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Model\Sdg;
use Goteo\Model\Category;
use Goteo\Model\Sphere;
use Goteo\Model\SocialCommitment;
use Goteo\Application\Session;


class SdgTest extends TestCase {
    private static $data = ['name' => 'test', 'description' => 'Sdg test text'];
    private static $category;
    private static $sphere;

    public function testInstance() {
        $ob = new Sdg();
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob);

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
        $ob = new Sdg();
        $errors = [];
        $this->assertFalse($ob->validate($errors), implode("\n", $errors));
        $errors = [];
        $ob = new Sdg(self::$data);
        $this->assertTrue($ob->validate($errors), implode("\n", $errors));
        $this->assertTrue($ob->save($errors), implode("\n", $errors));
        $ob = Sdg::get($ob->id);
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob);

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
        $this->assertStringEndsWith("/img/sdg/square/{$ob->id}.png", $ob->getIcon()->getLink());
        $this->assertFalse($ob->setIcon('testimage.png')->getIcon()->isAsset());
        $this->assertStringEndsWith('testimage.png', $ob->getIcon()->getLink());
    }

    /**
     * @depends testCreate
     */
    public function testCategoriesRelationships($ob) {
        $errors = [];
        $cat = new Category(['name' => 'sdg test category']);
        $this->assertTrue($cat->save($errors), implode("\n", $errors));
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->addCategories($cat));
        $cats = $ob->getCategories();
        $this->assertCount(1, $cats);
        $this->assertInstanceOf('\Goteo\Model\Category', $cats[0]);
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->removeCategories($cats));
        $this->assertCount(0, $ob->getCategories());
    }

    /**
     * @depends testCreate
     */
    public function testSpheresRelationships($ob) {
        $errors = [];
        $sphere = new Sphere(['name' => 'sdg test sphere']);
        $this->assertTrue($sphere->save($errors), implode("\n", $errors));
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->addSpheres($sphere));
        $spheres = $ob->getSpheres();
        $this->assertCount(1, $spheres);
        $this->assertInstanceOf('\Goteo\Model\Sphere', $spheres[0]);
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->removeSpheres($spheres));
        $this->assertCount(0, $ob->getSpheres());
    }

    /**
     * @depends testCreate
     */
    public function testSocialCommitmentRelationships($ob) {
        $errors = [];
        $social = new SocialCommitment(['name' => 'sdg test social']);
        $this->assertTrue($social->save($errors), implode("\n", $errors));
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->addSocialCommitments($social));
        $socials = $ob->getSocialCommitments();
        $this->assertCount(1, $socials);
        $this->assertInstanceOf('\Goteo\Model\SocialCommitment', $socials[0]);
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->removeSocialCommitments($socials));
        $this->assertCount(0, $ob->getSocialCommitments());
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
        $ob = Sdg::get($ob->id);
        $this->assertNull($ob);
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        Category::query("DELETE FROM category WHERE `id` = ?", self::$category);
        Sphere::query("DELETE FROM sphere WHERE `id` = ?", self::$sphere);
    }
}
