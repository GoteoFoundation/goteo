<?php

namespace Goteo\Model {

    class Category extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $description,
            $used; // numero de proyectos que usan la categoria

        /*
         *  Devuelve datos de una categoria
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        id,
                        name,
                        description
                    FROM    category
                    WHERE id = :id
                    ", array(':id' => $id));
                $category = $query->fetchObject(__CLASS__);

                return $category;
        }

        /*
         * Lista de categorias para proyectos
         * @TODO añadir el numero de usos
         */
        public static function getAll () {

            $list = array();

            $sql = "
                SELECT
                    id,
                    name,
                    description,
                    '1' as used
                FROM    category";

            $sql .= " ORDER BY name ASC";

            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $category) {
                $list[$category->id] = $category;
            }

            return $list;
        }

        public function validate (&$errors = array()) { 
            if (empty($this->name))
                $errors[] = 'Falta nombre';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'name',
                'description'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO category SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una catgoria de la tabla
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM category WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

    }
    
}