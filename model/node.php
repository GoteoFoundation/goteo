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
        public static function getAll ($filters = array()) {

            $list = array();

            $sqlFilter = "";
            if (!empty($filters['name'])) {
                $sqlFilter .= " AND ( name LIKE ('%{$filters['name']}%') )";
            }
            if (!empty($filters['status'])) {
                $active = $filters['status'] == 'active' ? '1' : '0';
                $sqlFilter .= " AND active = '$active'";
            }
            if (!empty($filters['admin'])) {
                $sqlFilter .= " AND admin = '{$filters['interest']}'";
            }

            $sql = static::query("
                SELECT
                    *
                FROM node
                WHERE id != 'goteo'
                    $sqlFilter
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
            if (empty($this->id))
                $errors[] = 'Falta Identificador';

            if (empty($this->name))
                $this->name = $this->id;

            if (!isset($this->active))
                $this->active = 0;

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
                'name'
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

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
         }

        /**
		 * Guarda lo imprescindible para crear el registro.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
         public function create (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'name',
                'admin',
                'active'
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

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
         }

    }
    
}