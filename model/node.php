<?php

namespace Goteo\Model {
    
    class Node extends \Goteo\Core\Model {

        public
            $id = null,
            $name,
            $logo,
            $image;



        /**
         * Obtener datos de un nodo
         * @param   type mixed  $id     Identificador
         * @return  type object         Objeto
         */
        static public function get ($id) {
                $sql = static::query("
                    SELECT
                        *
                    FROM node
                    WHERE id = :id
                    ", array(':id' => $id));
                $news = $sql->fetchObject(__CLASS__);

                return $news;
        }

        /*
         * Lista de nodos
         */
        public static function getAll () {

            $list = array();

            $sql = static::query("
                SELECT
                    *
                FROM node
                ORDER BY `name` ASC
                ");

            foreach ($sql->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $item) {
                $list[] = $item;
            }

            return $list;
        }

        /**
         * Validar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        public function validate (&$errors = array()) {
            if (empty($this->name))
                $errors[] = 'Falta nombre';

            if (empty($errors))
                return true;
            else
                return false;
        }

        /**
		 * Guardar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
         public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'name',
                'active',
                'url'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO node SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
         }

    }
    
}