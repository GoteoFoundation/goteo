<?php

/*
* Model for Node Sponsor
*/

namespace Goteo\Model\Node;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Model\Image;



class NodeSponsor extends \Goteo\Core\Model {

    protected $Table = 'node_sponsor';
    protected static $Table_static = 'node_sponsor';
    public
    $id,
    $node_id,
    $name,
    $url,
    $image,
    $order;


    public static function getLangFields() {
        return ['label'];
    }

    /**
     * Get data about node Sponsor
     *
     * @param   int    $id         check id.
     * @return  Workshop Sponsor object
     */
    static public function get($id) {
        $sql="SELECT
                    node_sponsor.*
              FROM node_sponsor
              WHERE node_sponsor.node_id = ?";
        $query = static::query($sql, array($id));
        $item = $query->fetchObject(__CLASS__);

        if($item) {
            return $item;
        }

        throw new ModelNotFoundException("Node sponsor not found for ID [$id]");
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
            'name',
            'url',
            'image',
            'url',
            'order'
        );

        try {
            //automatic $this->id assignation
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Node sponsor save error: " . $e->getMessage();
            return false;
        }
    }

    public function getImage() {
        if(!$this->imageInstance instanceOf Image) {
            $this->imageInstance = new Image($this->image);
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


