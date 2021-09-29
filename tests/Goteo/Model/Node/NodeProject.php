<?php


namespace Goteo\Model\Node\Tests;

use Goteo\Model\Node\NodeProject;
use Goteo\Application\Exception\ModelNotFoundException;

class NodeProjectTest extends \PHPUnit\Framework\TestCase {

  public function testInstance() {

      $ob = new NodeProject();

      $this->assertInstanceOf('\Goteo\Model\Node\NodeProject', $ob);

      return $ob;
  }

  /**
   * @depends testInstance
   */
  public function testValidate($ob) {
    $this->assertFalse($ob->validate(), print_r($errors, 1));
    $this->assertFalse($ob->save());
    return $ob;
  }

  public function testCreateNodeProject() {
    $node_id = get_test_node()->id;
    $project_id = get_test_project()->id;
    $ob = new NodeProject(['node_id' => $node_id, 'project_id' => $project_id]);
    $this->assertTrue($ob->validate($errors));
    $this->assertTrue($ob->save());

    return $ob;
  }

  /**
   * @depends testCreateNodeProject
   */
  public function testRemoveNodeProject($node_project) {
    $errors = array();
    $this->assertTrue($node_project->remove($errors), print_r($errors, 1));
    return $node_project;
  }

  /**
   * @depends testRemoveNodeProject
   */
  public function testNonExisting($node_project) {
      $this->expectException(ModelNotFoundException::class);
      $ob = NodeProject::get($node_project->node_id);
  }

    /**
     * Some cleanup
     */
    static function tearDownAfterClass(): void {
      delete_test_project();
      delete_test_node();
  }

}
