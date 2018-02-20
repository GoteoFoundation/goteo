<?php


namespace Goteo\Model\Tests;

use Goteo\TestCase;
use Goteo\Model\Node;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Application\Config;
use Goteo\Application\Lang;

class NodeTest extends TestCase {
    private static $related_tables = array('project' => 'node',
                    'user' => 'node',
                    'banner' => 'node',
                    'campaign' => 'node',
                    'faq' => 'node',
                    'info' => 'node',
                    'mail' => 'node',
                    'node_data' => 'node',
                    'node_lang' => 'id',
                    'invest_node' => ['user_node', 'project_node', 'invest_node'],
                    'patron' => 'node',
                    'post_node' => 'node',
                    'sponsor' => 'node',
                    'stories' => 'node',
                    );

    private static $data = array('id' => 'testnode2', 'name' => 'Test node 2', 'subtitle' => 'Test subtitle', 'description' => 'Test description');
    private static $trans_data = array('subtitle' => 'Test de subtítol', 'description' => 'Test descripció');

    public static function setUpBeforeClass() {
        Config::set('lang', 'es');
        Lang::setDefault('es');
        Lang::set('es');
    }

    public function testInstance() {
        \Goteo\Core\DB::cache(false);
        $ob = new Node();

        $this->assertInstanceOf('\Goteo\Model\Node', $ob);

        return $ob;
    }
    /**
     * @depends testInstance
     */
    public function testValidate($ob) {
        $this->assertFalse($ob->validate());
        $this->assertFalse($ob->save());
        //delete test node if exists
        try {
            $node = Node::get(self::$data['id']);
            $node->dbDelete();
        } catch(\Exception $e) {
            // node not exists, ok
        }
    }

    /**
     * @depends testValidate
     */
    public function testCreateNode() {
        $errors = array();
        $node = new Node(self::$data);
        $this->assertTrue($node->validate($errors), print_r($errors, 1));
        $this->assertNotFalse($node->create($errors), print_r($errors, 1));
        // die($node->id);
        $ob = Node::get($node->id);
        $this->assertInstanceOf('\Goteo\Model\Node', $ob);
        $this->assertEquals($ob->id, self::$data['id']);
        $this->assertEquals($ob->name, self::$data['name']);
        $this->assertEquals($ob->subtitle, self::$data['subtitle']);
        $this->assertEquals($ob->description, self::$data['description']);

        return $ob;
    }

    /**
     * @depends testCreateNode
     */
    public function testRenameNode($node) {
        try {
            $node->rebase('test node 3');
        }
        catch(\Exception $e) {
            $this->assertInstanceOf('\Goteo\Application\Exception\ModelException', $e);
        }

        $this->assertTrue($node->rebase('testnode3'));
        $this->assertEquals($node->id, 'testnode3');
        return $node;
    }

    /**
     * @depends testRenameNode
     */
    public function testSaveLanguages($ob) {
        $errors = [];
        $this->assertTrue($ob->setLang('ca', self::$trans_data, $errors), print_r($errors, 1));
        return $ob;
    }

    /**
     * @depends testSaveLanguages
     */
    public function testCheckLanguages($ob) {
        $new = Node::get($ob->id);
        $this->assertInstanceOf('Goteo\Model\Node', $new);
        $this->assertEquals(self::$data['subtitle'], $new->subtitle);
        $this->assertEquals(self::$data['description'], $new->description);
        Lang::set('ca');
        $new2 = Node::get($ob->id);
        $this->assertEquals(self::$trans_data['subtitle'], $new2->subtitle);
        $this->assertEquals(self::$trans_data['description'], $new2->description);
        Lang::set('es');
    }

    /**
     * @depends testRenameNode
     */
    public function testListing($ob) {
        $list = Node::getAll();
        $this->assertInternalType('array', $list);
        $new = end($list);
        $this->assertInstanceOf('Goteo\Model\Node', $new);
        $this->assertEquals(self::$data['subtitle'], $new->subtitle);
        $this->assertEquals(self::$data['description'], $new->description);

        Lang::set('ca');
        $list = Node::getAll();
        $this->assertInternalType('array', $list);

        $new2 = end($list);
        $this->assertEquals(self::$trans_data['subtitle'], $new2->subtitle);
        $this->assertEquals(self::$trans_data['description'], $new2->description);
        Lang::set('es');
    }


    /**
     * @depends testRenameNode
     */
    public function testConstrains($node) {
        $testnode = get_test_node();
        try {
            $node->rebase($testnode->id);
        }
        catch(\Exception $e) {
            $this->assertInstanceOf('\Goteo\Application\Exception\ModelException', $e);
        }
        delete_test_node();
        $this->assertTrue($node->rebase($testnode->id));
        $u = get_test_user();
        $p = get_test_project();

        $this->assertTrue($node->rebase('testnode2'));
        try {
        }
        catch(\Exception $e) {
            $this->assertInstanceOf('\Goteo\Application\Exception\ModelException', $e);
        }
        $user = User::get($u->id);
        $project = Project::get($p->id);
        $this->assertEquals('testnode2', $user->node);
        $this->assertEquals('testnode2', $project->node);
    }

    /**
     * @depends testCreateNode
     */
    public function testDeleteNode($node) {
        try {
            $node->dbDelete();
        }
        catch(\Exception $e) {
            $this->assertInstanceOf('\PDOException', $e);
        }
        delete_test_project();
        delete_test_user();

        $this->assertTrue($node->dbDelete());

        return $node;
    }

    public function testNonExisting() {
        try {
            $ob = Node::get(self::$data['id']);
        }catch(\Exception $e) {
            $this->assertInstanceOf('\Goteo\Application\Exception\ModelNotFoundException', $e);
        }
        try {
            $ob = Node::get('non-existing-project');
        }catch(\Exception $e) {
            $this->assertInstanceOf('\Goteo\Application\Exception\ModelNotFoundException', $e);
        }
    }

    public function testCleanNodeRelated() {
        foreach(self::$related_tables as $tb => $fields) {
            if(!is_array($fields)) $fields = [$fields];
            foreach($fields as $field) {
                $this->assertEquals(0, Node::query("SELECT COUNT(*) FROM `$tb`  WHERE `$field` NOT IN (SELECT id FROM node)")->fetchColumn(), "DB incoherences in table [$tb], Please run SQL command:\nDELETE FROM  `$tb` WHERE `$field` NOT IN (SELECT id FROM node)");
            }
        }
    }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass() {
        delete_test_project();
        delete_test_user();
        delete_test_node();
    }
}
