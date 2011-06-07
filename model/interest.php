<?php

namespace Goteo\Model {

    class Interest extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $description,
            $used; // numero de usuarios que tienen este interés

        /*
         *  Devuelve datos de un interés
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        id,
                        name,
                        description
                    FROM    interest
                    WHERE id = :id
                    ", array(':id' => $id));
                $interest = $query->fetchObject(__CLASS__);

                return $interest;
        }

        /*
         * Lista de intereses para usuarios
         * @TODO añadir el numero de usos
         */
        public static function getAll () {

            $list = array();

            $sql = "
                SELECT
                    id,
                    name,
                    description,
                    (   SELECT
                            COUNT(user_interest.user)
                        FROM user_interest
                        WHERE user_interest.interest = interest.id
                    ) as used,
                    `order`
                FROM    interest
                ORDER BY name ASC";

            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $interest) {
                $list[$interest->id] = $interest;
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
                $sql = "REPLACE INTO interest SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar un interes de la tabla
         */
        public static function delete ($id) {

            $sql = "DELETE FROM interest WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up ($id) {

            $query = self::query('SELECT `order` FROM interest WHERE id = :id'
                , array(':id'=>$id));
            $order = $query->fetchColumn(0);

            $order--;
            if ($order < 1)
                $order = 1;

            $sql = "UPDATE interest SET `order`=:order WHERE id = :id";
            if (self::query($sql, array(':order'=>$order, ':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que salga despues  (aumentar el order)
         */
        public static function down ($id) {

            $query = self::query('SELECT `order` FROM interest WHERE id = :id'
                , array(':id'=>$id));
            $order = $query->fetchColumn(0);

            $order++;

            $sql = "UPDATE interest SET `order`=:order WHERE id = :id";
            if (self::query($sql, array(':order'=>$order, ':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Orden para añadirlo al final
         */
        public static function next () {
            $query = self::query('SELECT MAX(`order`) FROM interest');
            $order = $query->fetchColumn(0);
            return ++$order;

        }
    }

}