<?php

/*
* Model for Node term
*/

namespace Goteo\Model\Node;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Model\Image;



class NodeTerm extends \Goteo\Core\Model {

    protected $Table = 'node_term';
    protected static $Table_static = 'node_term';
    public
    $id,
    $node_id,
    $title,
    $description,
    $icon,
    $order;

    public static function getLangFields() {
        return ['title', 'description'];
    }


    /**
     * Get data about node term
     *
     * @param   int    $id         check id.
     * @return  Workshop term object
     */
    static public function get($id) {
        $sql="SELECT
                    node_term.*
              FROM node_term
              WHERE node_term.node_id = ?";
        $query = static::query($sql, array($id));
        $item = $query->fetchObject(__CLASS__);

        if($item) {
          return $item;
        }

        throw new ModelNotFoundException("Node term not found for ID [$id]");
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
            'order'
        );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Node term save error: " . $e->getMessage();
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


