<?php

namespace Goteo\Model\Tests;

use Goteo\Model\Category;
use Goteo\Model\Footprint;
use Goteo\Model\Sdg;
use Goteo\Model\Sphere;
use Goteo\Model\SocialCommitment;
use Goteo\TestCase;

class FootprintTest extends TestCase {
	private static $data = ['name' => 'test', 'description' => 'Footprint test text'];
	private static $sdg;
	private static $cat;
	private static $sphere;
	private static $social;

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

		foreach (self::$data as $key => $val) {
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
		$this->assertStringEndsWith("/img/footprint/{$ob->id}.svg", $ob->getIcon()->getLink());
		$this->assertFalse($ob->setIcon('testimage.png')->getIcon()->isAsset());
		$this->assertStringEndsWith('testimage.png', $ob->getIcon()->getLink());
	}

	/**
	 * @depends testCreate
	 */
	public function testSdgRelationships($ob) {
		$errors = [];
		$sdg = new Sdg(['name' => 'sdg test sdg']);
		$this->assertTrue($sdg->save($errors), implode("\n", $errors));
        $this->assertInstanceOf('\Goteo\Model\Footprint', $ob->addSdgs($sdg));
        $sdgs = $ob->getSdgs();
        $this->assertCount(1, $sdgs);
        $this->assertInstanceOf('\Goteo\Model\Sdg', $sdgs[0]);
        self::$sdg = $sdgs[0]->id;
        // repeated assignment should'nt be a problem
        $this->assertInstanceOf('\Goteo\Model\Footprint', $ob->addSdgs($sdgs));
        $this->assertCount(1, $ob->getSdgs());
        //
        $this->assertInstanceOf('\Goteo\Model\Footprint', $ob->replaceSdgs($sdgs));
        $this->assertCount(1, $ob->getSdgs());

		return $ob;
	}

    /**
     * @depends testSdgRelationships
     */
    public function testGetFootprintFromSdgs($ob) {
        $errors = [];
        $foot = Footprint::getFromSdgs(self::$sdg);
        $this->assertGreaterThanOrEqual(1, $foot);
        $this->assertInstanceOf('\Goteo\Model\Footprint', $foot[0]);
    }

    /**
     * @depends testSdgRelationships
     */
    public function testGetFootprintFromCategories($ob) {
        $errors = [];
        $cat = new Category(['name' => 'footprint category test']);
        $this->assertTrue($cat->save($errors), implode("\n", $errors));
        self::$cat = $cat->id;
        $this->assertInstanceOf('\Goteo\Model\Category', $cat->addSdgs(self::$sdg));
        $this->assertCount(1, $cat->getSdgs());
        $foot = Footprint::getFromCategories($cat);
        $this->assertGreaterThanOrEqual(1, count($foot));
        $this->assertInstanceOf('\Goteo\Model\Footprint', $foot[0]);
    }

    /**
     * @depends testSdgRelationships
     */
    public function testGetFootprintFromSpheres($ob) {
        $errors = [];
        $sph = new Sphere(['name' => 'footprint sphere test']);
        $this->assertTrue($sph->save($errors), implode("\n", $errors));
        self::$sphere = $sph->id;
        $this->assertInstanceOf('\Goteo\Model\Sphere', $sph->addSdgs(self::$sdg));
        $this->assertCount(1, $sph->getSdgs());
        $foot = Footprint::getFromSpheres($sph);
        $this->assertGreaterThanOrEqual(1, count($foot));
        $this->assertInstanceOf('\Goteo\Model\Footprint', $foot[0]);
    }

    /**
     * @depends testSdgRelationships
     */
    public function testGetFootprintFromSocialCommitments($ob) {
        $errors = [];
        $social = new SocialCommitment(['name' => 'footprint sphere test']);
        $this->assertTrue($social->save($errors), implode("\n", $errors));
        self::$social = $social->id;
        $this->assertInstanceOf('\Goteo\Model\SocialCommitment', $social->addSdgs(self::$sdg));
        $this->assertCount(1, $social->getSdgs());
        $foot = Footprint::getFromSocialCommitments($social);
        $this->assertGreaterThanOrEqual(1, count($foot));
        $this->assertInstanceOf('\Goteo\Model\Footprint', $foot[0]);
    }

    /**
     * @depends testSdgRelationships
     */
    public function testRemoveSdgRelationships($ob) {
        $this->assertCount(1, $ob->getSdgs());
        $this->assertInstanceOf('\Goteo\Model\Footprint', $ob->removeSdgs(self::$sdg));
        $this->assertCount(0, $ob->getSdgs());
        // repeated unassignment should'nt be a problem
        $this->assertInstanceOf('\Goteo\Model\Footprint', $ob->removeSdgs(self::$sdg));
        $this->assertCount(0, $ob->getSdgs());

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

	/**
	 * Some cleanup
	 */
	static function tearDownAfterClass(): void {
		Sdg::query("DELETE FROM sdg WHERE `id` = ?", self::$sdg);
        Sdg::query("DELETE FROM category WHERE `id` = ?", self::$cat);
        Sdg::query("DELETE FROM sphere WHERE `id` = ?", self::$sphere);
		Sdg::query("DELETE FROM social_commitment WHERE `id` = ?", self::$social);
	}
}
