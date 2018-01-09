<?php

/*
* Model for tax relief
*/

namespace Goteo\Model;

use Goteo\Application\Exception\ModelNotFoundException;

class Sphere extends \Goteo\Core\Model {

    public
    $id,
    $name,
    $image;


    public static function getLangFields() {
        return ['name'];
    }

    /**
     * Get data about a sphere
     *
     * @param   int    $id         sphere id.
     * @return  Sphere object
     */
    static public function get($id) {
        $sql="SELECT
                    sphere.*
              FROM sphere
              WHERE sphere.id = ?";
        $query = static::query($sql, array($id));
        $item = $query->fetchObject(__CLASS__);

        if($item) {
            if($item->image)
                    $item->image = Image::get($item->image);
            return $item;
        }

        throw new ModelNotFoundException("Sphere not found for ID [$id]");
    }


    public function getImage() {
        if($this->image instanceOf Image) return $this->image;
        if($this->image) {
            $this->image = Image::get($this->image);
        } else {
            $this->image = new Image();
        }
        return $this->image;
    }
    /**
     * Sphere list
     *
     * @param  array  $filters
     * @return mixed            Array of reliefs
     */
    public static function getAll($filters = array()) {

        $values = array();

        $list = array();

        $sqlFilter = "";
        $and = " WHERE";

        $sql = "SELECT sphere.*
                FROM sphere
                $sqlFilter
                ORDER BY name ASC
                ";

        $query = self::query($sql, $values);
        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
            $list[] = $item;
        }
        return $list;
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

        // Save opcional image
        if (is_array($this->image) && !empty($this->image['name'])) {
            $image = new Image($this->image);

            if ($image->save($errors)) {
                $this->image = $image->id;
            } else {
                \Goteo\Application\Message::error(Text::get('image-upload-fail') . implode(', ', $errors));
                $this->image = '';
            }
        }
        if (is_null($this->image)) {
            $this->image = '';
        }

        $fields = array(
            // 'id',
            'name',
            'image'
        );

        try {
            $this->dbInsertUpdate($fields);

            return true;
        } catch(\PDOException $e) {
            $errors[] = "Sphere save error: " . $e->getMessage();
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

        return true;
    }


}


