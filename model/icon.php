<?php

namespace Goteo\Model {

    use Goteo\Library\Text;

    class Icon extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $description,
            $group;

        /*
         *  Devuelve datos de un icono
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        id,
                        name,
                        description,
                        `group`
                    FROM    icon
                    WHERE id = :id
                    ", array(':id' => $id));
                $icon = $query->fetchObject(__CLASS__);

                return $icon;
        }

        /*
         * Lista de proyectos iconos
         */
        public static function getAll ($group = '') {

            $sql = "
                SELECT
                    id,
                    name,
                    description,
                    `group`
                FROM    icon";

            if ($group != '') {
                // de un grupo o de todos
                $sql .= " WHERE `group` = :group OR `group` IS NULL";
            }

            $sql .= " ORDER BY name ASC";

            $query = static::query($sql, array(':group' => $group));
            
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

            if (empty($this->group)) {
                $this->group = null;
            }

            $fields = array(
                'id',
                'name',
                'description',
                'group'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO icon SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar un icono
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM icon WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        public static function groups () {
            return array(
                'social' => 'Retorno colectivo',
                'individual' => 'Recompensa individual'
            );
        }


    }
    
}