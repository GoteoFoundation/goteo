<?php


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Model\Sdg;
use Goteo\Model\Category;
use Goteo\Model\Sphere;
use Goteo\Model\Footprint;
use Goteo\Model\SocialCommitment;
use Goteo\Application\Session;


class SdgTest extends TestCase {
    private static $data = ['name' => 'test', 'description' => 'Sdg test text'];
    private static $category;
    private static $sphere;
    private static $socialcommitment;
    private static $footprint;

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
        self::$category = $cats[0]->id;
        // repeated assignment should'nt be a problem
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->addCategories($cats));
        $this->assertCount(1, $ob->getCategories());
        //
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->replaceCategories($cats));
        $this->assertCount(1, $ob->getCategories());

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
        self::$sphere = $spheres[0]->id;
        // repeated assignment should'nt be a problem
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->addSpheres($spheres));
        $this->assertCount(1, $ob->getSpheres());
        //
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->replaceSpheres($spheres));
        $this->assertCount(1, $ob->getSpheres());


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
        self::$socialcommitment = $socials[0]->id;
        // repeated assignment should'nt be a problem
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->addSocialCommitments($cats));
        $this->assertCount(1, $ob->getSocialCommitments());
        //
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->replaceSocialCommitments($cats));
        $this->assertCount(1, $ob->getSocialCommitments());


        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->removeSocialCommitments($socials));
        $this->assertCount(0, $ob->getSocialCommitments());
    }

    /**
     * @depends testCreate
     */
    public function testFootprintRelationships($ob) {
        $errors = [];
        $foot = new Footprint(['name' => 'sdg test footprint']);
        $this->assertTrue($foot->save($errors), implode("\n", $errors));
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->addFootprints($foot));
        $footprints = $ob->getFootprints();
        $this->assertCount(1, $footprints);
        $this->assertInstanceOf('\Goteo\Model\Footprint', $footprints[0]);
        self::$footprint = $footprints[0]->id;
        // repeated assignment should'nt be a problem
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->addFootprints($cats));
        $this->assertCount(1, $ob->getFootprints());
        //
        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->replaceFootprints($cats));
        $this->assertCount(1, $ob->getFootprints());


        $this->assertInstanceOf('\Goteo\Model\Sdg', $ob->removeFootprints($footprints));
        $this->assertCount(0, $ob->getFootprints());
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
    static function tearDownAfterClass(): void {
        Category::query("DELETE FROM category WHERE `id` = ?", self::$category);
        Sphere::query("DELETE FROM sphere WHERE `id` = ?", self::$sphere);
        SocialCommitment::query("DELETE FROM social_commitment WHERE `id` = ?", self::$socialcommitment);
        Footprint::query("DELETE FROM footprint WHERE `id` = ?", self::$footprint);
    }
}
