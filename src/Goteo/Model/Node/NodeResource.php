<?php

/*
* Model for Node Resource
*/

namespace Goteo\Model\Node;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Model\Image;



class NodeResource extends \Goteo\Core\Model {

    protected $Table = 'node_resource';
    protected static $Table_static = 'node_resource';
    public
    $id,
    $node_id,
    $title,
    $icon,
    $description,
    $action,
    $action_url,
    $action_icon,
    $order;

    public static function getLangFields() {
        return ['title', 'description', 'action', 'action_url'];
    }


    /**
     * Get data about node resource
     *
     * @param   int    $id         check id.
     * @return  Workshop resource object
     */
    static public function get($id) {
        $sql="SELECT
                    node_resource.*
              FROM node_resource
              WHERE node_resource.node_id = ?";
        $query = static::query($sql, array($id));
        $item = $query->fetchObject(__CLASS__);

        if($item) {
            return $item;
        }

        throw new ModelNotFoundException("Node resource not found for ID [$id]");
    }

    /**
     * Node Resource listing
     *
     * @param array filters
     * @param string node id
     * @param int limit items per page or 0 for unlimited
     * @param int page
     * @param int pages
     * @return array of programs instances
     */
    static public function getList($filters = [], $offset = 0, $limit = 10, $count = false, $lang = null) {

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $filter = [];
        $values = [];

        if ($filters['node']) {
            $filter[] = "node_resource.node_id = :node";
            $values[':node'] = $filters['node'];
        }

        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        $sql="SELECT
                  node_resource.id as id,
                  node_resource.node_id as node,
                  $fields,
                  node_resource.action_icon as action_icon,
                  node_resource.order
              FROM node_resource
              $joins
              $sql
              ORDER BY node_resource.order ASC
              LIMIT $offset, $limit";
         //die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

   
    /**
     * Save.
     *
     * @param   type array  $errors
     * @return  type bool   true|false
     */
    public function save(&$errors = array()) {

        if (!$this->validate($errors))
            return false;

        $fields = array(
            'id',
            'node_id',
            'title',
            'icon',
            'description',
            'action',
            'action_url',
            'action_icon',
            'order'
        );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Node resource save error: " . $e->getMessage();
            return false;
        }
    }

    public function getIcon() {
        if(!$this->imageInstance instanceOf Image) {
            $this->imageInstance = new Image($this->icon);
        }
        return $this->imageInstance;
    }

    public function getActionIcon() {
        if(!$this->imageInstance instanceOf Image) {
            $this->imageInstance = new Image($this->action_icon);
        }
        return $this->imageInstance;
    }


    /**
     * Validate.
     *
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function validate(&$errors = array()) {
        if(empty($this->name)) {
            $errors[] = "Empty name";
        }
        return empty($errors);
    }


}


