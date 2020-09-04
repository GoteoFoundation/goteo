<?php

/*
* Model for Node Resource
*/

namespace Goteo\Model\Node;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Model\Image;



class NodeResourceCategory extends \Goteo\Core\Model {

    protected $Table = 'node_resource_category';
    protected static $Table_static = 'node_resource_category';
    public
    $id,
    $name,
    $slug,
    $icon,
    $lang,
    $order;

    public static function getLangFields() {
        return ['name'];
    }


    /**
     * Get data about node resource category
     *
     * @param   int    $id         check id.
     * @return  Workshop resource object
     */
    static public function get($id) {

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql="SELECT
                    node_resource_category.id,
                    $fields,
                    node_resource_category.icon
              FROM node_resource_category
              $joins
              WHERE node_resource_category.id = ?";
        $query = static::query($sql, array($id));
        $item = $query->fetchObject(__CLASS__);

        if($item) {
            return $item;
        }

        throw new ModelNotFoundException("Node resource not found for ID [$id]");
    }

    static public function getIdBySlug($slug){
        $sql="SELECT
                    node_resource_category.id
              FROM node_resource_category
              WHERE node_resource_category.slug = ?";

        $query = static::query($sql, array($slug));

        $id = $query->fetchColumn();

        if($id) {
            return $id;
        }

        throw new ModelNotFoundException("Node resource not found for SLUG [$slug]");
    }

    /**
     * Node Resource category listing
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

        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        $sql="SELECT
                  node_resource_category.id as id,
                  $fields,
                  node_resource_category.slug as slug,
                  node_resource_category.icon as icon,
                  node_resource_category.lang,
                  node_resource_category.order
              FROM node_resource_category
              $joins
              $sql
              ORDER BY node_resource_category.order ASC
              LIMIT $offset, $limit";
         //die(\sqldbg($sql, $values));
        $query = static::query($sql, $values);
        return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }



    /**
     * GetIcon.
     *
     * @param   type array  $errors
     * @return  type bool   true|false
     */
    public function getIcon() {
      if(!$this->iconImageInstance instanceOf Image) {
          $this->iconImageInstance = new Image($this->icon);
      }
      return $this->iconImageInstance;
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
            'name',
            'icon',
            'lang',
            'order'
        );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Node resource category save error: " . $e->getMessage();
            return false;
        }
    }

    /*public function getIcon() {
        if(!$this->imageInstance instanceOf Image) {
            $this->imageInstance = new Image($this->icon);
        }
        return $this->imageInstance;
    }*/


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


