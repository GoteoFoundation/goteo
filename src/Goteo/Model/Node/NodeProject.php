<?php

namespace Goteo\Model\Node;

use Goteo\Core\Model;

class NodeProject extends Model {

    protected $Table = 'node_project';
    protected static $Table_static = 'node_project';

    public
      $node_id,
      $project_id,
      $order;

    static public function get($id): NodeProject
     {
        $sql = "SELECT *
                FROM node_project np
                LEFT JOIN project p ON p.id = np.project_id
                WHERE (np.node_id = ? or p.node = ?) ";
        $query = static::query($sql, [$id]);
        return $query->fetchObject( __CLASS__ );
    }

    /**
     * @return NodeProject[] | int
     */
    static public function getList(array $filters = [], int $offset = 0, int $limit = 10, bool $count = false, string $lang = null)
    {
        $filter = [];
        $values = [];

        if ($filters['node']) {
            $filter[] = "np.node_id = :node";
            $values[':node'] = $filters['node'];
        }

        if ($filters['project']) {
            $filter[] = "np.project_id = :project";
            $values[':project'] = $filters['project'];
        }

        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        if ($count) {
            $sql = "SELECT COUNT(np.project_id)
            FROM node_project np
            INNER JOIN project p on p.id = np.project_id
            $sql";
            return (int) self::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT
                    p.id,
                    np.project_id,
                    np.node_id,
                    p.name,
                    p.image
                FROM node_project np
                INNER JOIN project p ON p.id = np.project_id
                $sql
                ORDER BY np.order ASC
                LIMIT $offset, $limit";

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


