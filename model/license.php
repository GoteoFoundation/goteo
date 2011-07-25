<?php

namespace Goteo\Model {

    use Goteo\Library\Check;
    
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
                        url,
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
        public static function getAll ($icon = null, $group = null) {

            // por ahora el grupo es 'open' o no.

            // icon es si esta en relacion en icon_license

            $sql = "
                SELECT
                    id,
                    name,
                    description,
                    url,
                    `group`,
                    `order`
                FROM    license
                ";

            if (!empty($icon)) {
                // de un grupo o de todos
                $sql .= "INNER JOIN icon_license
                    ON icon_license.license = license.id
                    AND icon_license.icon = :icon
                    ";
                $values[':icon'] = $icon;
            }

            if (!empty($group)) {
                // de un grupo o de todos
                $sql .= "WHERE `group` = :group
                    ";
                $values[':group'] = $group;
            }

            $sql .= "ORDER BY `order` ASC, name ASC
                ";
            
            $query = static::query($sql, $values);
            
            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }

        public function validate (&$errors = array()) { 
            if (empty($this->name))
                $errors[] = 'Falta nombre';
                //Text::get('mandatory-license-name');

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
                'url',
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
            return Check::reorder($id, 'up', 'license', 'id', 'order');
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id) {
            return Check::reorder($id, 'down', 'license', 'id', 'order');
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
                'regular' => 'Normal',
                'open' => 'Abierto'
            );
        }


    }
    
}