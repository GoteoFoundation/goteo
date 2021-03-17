<?php

/*
* Model for Node project
*/

namespace Goteo\Model\Node;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Config;



class NodeProject extends \Goteo\Core\Model {

    protected $Table = 'node_project';
    protected static $Table_static = 'node_project';
    
    public
      $node_id,
      $project_id,
      $order;

    /**
     * Get data about projects in  node project
     *
     * @param   mixed    $id node id.
     * @return  NodeProject object
     */
    static public function get($id) {

        $sql="SELECT *
              FROM node_project
              $joins
              WHERE node_project.node_id = ?";
        $query = static::query($sql, array($id));
        $node_projects = $query->fetchAll( \PDO::FETCH_CLASS, __CLASS__);

        if(!$node_projects) {
            throw new ModelNotFoundException("Node project not found for ID [$id]");
        }
        
        return $item;
    }

    /**
     * Node projects listing
     *
     * @param array filters
     * @param string node id
     * @param int limit items per page or 0 for unlimited
     * @param int page
     * @param int pages
     * @return array of projects instances
     */
    static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) {

        $filter = [];
        $values = [];

        if ($filters['node']) {
            $filter[] = "node_project.node_id = :node";
            $values[':node'] = $filters['node'];
        }

        if ($filters['project']) {
            $filter[] = "node_project.project_id = :project";
            $values[':project'] = $filters['project'];
        }

        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        if ($count) {
            $sql = "SELECT COUNT(node_project.id)
            FROM node_project
            $sql";
            return (int) self::query($sql, $values)->fetchColumn();
        }

        $sql="SELECT *
              FROM node_project
              $sql
              ORDER BY node_project.order ASC
              LIMIT $offset, $limit";
        // die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }
   
    /**
     * Save.
     *
     * @param   type array  $errors
     * @return  type bool   true|false
     */
		public function save (&$errors = array()) {
      if (!$this->validate($errors)) return false;

      try {
        $sql = "REPLACE INTO node_project (node_id, project_id) VALUES(:node, :project)";
        $values = array(':node'=>$this->node_id, ':project'=>$this->project_id);
        if (self::query($sql, $values)) {
            return true;
        } else {
            $errors[] = "$sql <pre>".print_r($values, true)."</pre>";
        }
      } catch(\PDOException $e) {
        $errors[] = $e->getMessage();
        return false;
      }
      return empty($errors);
    }

    /**
     * Validate.
     *
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function validate(&$errors = array()) {
      if (empty($this->node_id))
          $errors[] = 'There is no node to save';

      if (empty($this->project_id))
          $errors[] = 'There is no project to save';

      return empty($errors);
    }

    public function remove($errors = array()) {
        if(empty($this->node_id))
            $errors[] = "There is no node specified";

        if (empty($this->project_id))
            $errors[] = "There is no node specified";

        if ($errors)
            return false;

        try {
          $sql = 'DELETE FROM node_project WHERE node_id = :node and project_id = :project';
          $values = [':node' => $this->node_id, ':project' => $this->project_id];
            self::query($sql, $values);
        } catch (\PDOException $e) {
            $errors[] = $e->getMessage();
            return false;
        }

        return true;
      }

}


