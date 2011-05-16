<?php

namespace Goteo\Model {

    class License extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $description,
            $group,
            $order;

        /*
         *  Devuelve datos de un destacado
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        id,
                        name,
                        description,
                        `group`,
                        `order`
                    FROM    license
                    WHERE id = :id
                    ", array(':id' => $id));
                $license = $query->fetchObject(__CLASS__);

                $query = static::query("
                    SELECT
                        icon
                    FROM    icon_license
                    WHERE license = :license
                    ", array(':license' => $id));
                foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $icon) {
                    $license->icons[] = $icon['icon'];
                }

                return $license;
        }

        /*
         * Lista de licencias
         */
        public static function getAll ($group = '') {

            $sql = "
                SELECT
                    id,
                    name,
                    description,
                    `group`,
                    `order`
                FROM    license";

            if ($group != '') {
                // de un grupo o de todos
                $sql .= " WHERE `group` = :group";
            }

            $sql .= "
                ORDER BY `order` ASC, name ASC
                ";
            
            $query = static::query($sql, array('group'=>$group));
            
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
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
                'description',
                'group',
                'order'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO license SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                // y los iconos con los que está relacionada
                self::query("DELETE FROM icon_license WHERE license = ?", array($this->id));

                foreach ($this->icons as $icon) {
                    self::query("INSERT INTO icon_license SET icon = :icon, license = :license",
                        array(':icon' => $icon, ':license' => $this->id));
                }

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una pregunta
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM license WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                self::query("DELETE FROM icon_license WHERE license = ?", array($id));
                
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que una pregunta salga antes  (disminuir el order)
         */
        public static function up ($id) {

            $query = self::query('SELECT `order` FROM license WHERE id = :id'
                , array(':id'=>$id));
            $order = $query->fetchColumn(0);

            $order--;
            if ($order < 1)
                $order = 1;

            $sql = "UPDATE license SET `order`=:order WHERE id = :id";
            if (self::query($sql, array(':order'=>$order, ':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id) {

            $query = self::query('SELECT `order` FROM license WHERE id = :id'
                , array(':id'=>$id));
            $order = $query->fetchColumn(0);

            $order++;

            $sql = "UPDATE license SET `order`=:order WHERE id = :id";
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
            $query = self::query('SELECT MAX(`order`) FROM license'
                , array(':group'=>$group, ':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

        public static function groups () {
            return array(
                '1' => 'Más abierta',
                '2' => 'Menos abierta'
            );
        }


    }
    
}