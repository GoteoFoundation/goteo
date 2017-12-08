<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Model;

use Goteo\Core\Model;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;

/**
 * Origin Model
 */
class Origin extends \Goteo\Core\Model {
    public $id,
           $tag ,
           $category ,
           $type ,
           $project_id,
           $invest_id,
           $call_id,
           $counter = 0,
           $created_at,
           $modified_at;

    /**
     * Get instance of origin already in the table by action
     * @return [type] [description]
     */
    static public function get($id) {
        if ($query = static::query("SELECT * FROM `origin` WHERE `id` = ?", $id)) {
            if( $origin = $query->fetchObject(__CLASS__) )
                return $origin;
        }
        throw new ModelNotFoundException("Origin [$id] not found");
    }

    /**
     * Get instance of origin already in the table or creates a new one
     * if not exists. Search by any given array of key => values
     * @return [type] [description]
     */
    static public function getFromArray(array $array) {
        $values = [];
        $search = [];
        foreach($array as $key => $val) {
            $values[":$key"] = $val;
            $search[] = "`$key` = :$key";
        }
        if ($query = static::query("SELECT * FROM `origin` WHERE " . implode(' AND ', $search), $values)) {
            if( $origin = $query->fetchObject(__CLASS__) )
                return $origin;
        }
        return new self($array);
    }


    /**
     * Guardar.
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function save(&$errors = array()) {

        if(!$this->validate($errors)) return false;

        if(!$this->created_at) $this->created_at = date('Y-m-d H:i:s');
        // Always increment counter
        $this->counter++;

        try {
            $this->dbInsertUpdate(['tag', 'category', 'type', 'project_id', 'invest_id', 'call_id', 'counter', 'created_at']);
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving origin: ' . $e->getMessage();
        }

        return false;
    }

    /**
     * Validar. check if origin is not-duplicated
     * @param   type array  $errors     Errores devueltos pasados por referencia.
     * @return  type bool   true|false
     */
    public function validate(&$errors = array()) {
        return empty($errors);
    }

}
