<?php

/*
* Model for Workshop Sponsor
*/

namespace Goteo\Model\Workshop;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Model\Image;



class WorkshopSponsor extends \Goteo\Core\Model {

    const TYPE_FOOTER = 'footer';
    const TYPE_SIDE = 'side';

    protected $Table = 'workshop_sponsor';
    protected static $Table_static = 'workshop_sponsor';
    public
    $id,
    $workshop,
    $name,
    $type,
    $url,
    $image,
    $order;

    


    /**
     * Get data about Workshop Sponsor
     *
     * @param   int    $id         check id.
     * @return  Workshop Sponsor object
     */
    static public function get($id) {
        $sql="SELECT
                    workshop_sponsor.*
              FROM workshop_sponsor
              WHERE workshop_sponsor.id = ?";
        $query = static::query($sql, array($id));
        $item = $query->fetchObject(__CLASS__);

        if($item) {
            return $item;
        }

        throw new ModelNotFoundException("Workshop sponsor not found for ID [$id]");
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
            'workshop',
            'name',
            'type',
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
            $errors[] = "Workshop sponsor save error: " . $e->getMessage();
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
            $errors[] = "Emtpy name";
        }
        return empty($errors);
    }


}


