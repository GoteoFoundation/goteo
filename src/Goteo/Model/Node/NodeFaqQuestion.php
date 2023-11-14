<?php

/*
* Model for Node Faq
*/

namespace Goteo\Model\Node;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Model\Image;



class NodeFaqQuestion extends \Goteo\Core\Model {

    protected $Table = 'node_faq_question';
    protected static $Table_static = 'node_faq_question';
    public
    $id,
    $node_faq,
    $title,
    $description,
    $icon,
    $order;

    public static function getLangFields() {
        return ['title', 'description'];
    }


    /**
     * Get data about node faq question
     *
     * @param   int    $id         check id.
     * @return  Workshop faq object
     */
    static public function get($id) {
        $sql="SELECT
                    node_faq_question.*
              FROM node_faq_question
              WHERE node_faq_question.node_id = ?";
        $query = static::query($sql, array($id));
        $item = $query->fetchObject(__CLASS__);

        if($item) {
          return $item;
        }

        throw new ModelNotFoundException("Node faq not found for ID [$id]");
    }

     /**
     * Node Faq Download listing
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
            $filter[] = "node_faq_question.node_id = :node";
            $values[':node'] = $filters['node'];
        }

         if ($filters['node_faq']) {
            $filter[] = "node_faq_question.node_faq = :node_faq";
            $values[':node_faq'] = $filters['node_faq'];
        }

        if($filter) {
            $sql = " WHERE " . implode(' AND ', $filter);
        }

        if ($count) {
            $sql = "SELECT COUNT(*) FROM node_faq_question $joins $sql";
            return (int) static::query($sql, $values)->fetchColumn();
        }

        $sql="SELECT
                  node_faq_question.id as id,
                  $fields,
                  node_faq_question.order,
                  node_faq_question.node_faq
              FROM node_faq_question
              $joins
              $sql
              ORDER BY node_faq_question.order ASC
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
    public function save(&$errors = array()) {

        if (!$this->validate($errors))
            return false;

        $fields = array(
            'id',
            'node_faq',
            'title',
            'icon',
            'description',
            'order'
        );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Node faq save error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Validate.
     *
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function validate(&$errors = array()) {
        return empty($errors);
    }


}


