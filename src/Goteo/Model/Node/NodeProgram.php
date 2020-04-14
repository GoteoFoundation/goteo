<?php

/*
* Model for Node program
*/

namespace Goteo\Model\Node;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Model\Image;



class NodeProgram extends \Goteo\Core\Model {

    protected $Table = 'node_program';
    protected static $Table_static = 'node_program';
    public
    $id,
    $node_id,
    $title,
    $subtitle,
    $description,
    $icon,
    $action,
    $action_url,
    $date,
    $order;

    public static function getLangFields() {
        return ['title', 'subtitle', 'description', 'action', 'action_url'];
    }


    /**
     * Get data about node program
     *
     * @param   int    $id         check id.
     * @return  Workshop program object
     */
    static public function get($id) {
        $sql="SELECT
                    node_program.*
              FROM node_program
              WHERE node_program.node_id = ?";
        $query = static::query($sql, array($id));
        $item = $query->fetchObject(__CLASS__);

        if($item) {
          return $item;
        }

        throw new ModelNotFoundException("Node program not found for ID [$id]");
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
            $errors[] = "Node program save error: " . $e->getMessage();
            return false;
        }
    }

    public function getIcon() {
        if(!$this->imageInstance instanceOf Image) {
            $this->imageInstance = new Image($this->icon);
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
        return empty($errors);
    }


}


