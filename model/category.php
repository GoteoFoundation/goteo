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
                    category.id,
                    category.name,
                    category.description,
                    (   SELECT 
                            COUNT(project_category.project)
                        FROM project_category
                        WHERE project_category.category = category.id
                    ) as used,
                    `order`
                FROM    category
                ORDER BY name ASC
                ";

            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $category) {
                $list[$category->id] = $category;
            }

            return $list;
        }

        /**
         * Get all categories used in published projects
         *
         * @param void
         * @return array
         */
		public static function getList () {
            $array = array ();
            try {
                $sql = "SELECT id, name
                        FROM category
                        INNER JOIN project_category
                            ON category.id = project_category.category
                        GROUP BY id
                        ORDER BY name ASC";

                $query = static::query($sql);
                $categories = $query->fetchAll();
                foreach ($categories as $cat) {
                    $array[$cat[0]] = $cat[1];
                }

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
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

        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up ($id) {

            $query = self::query('SELECT `order` FROM category WHERE id = :id'
                , array(':id'=>$id));
            $order = $query->fetchColumn(0);

            $order--;
            if ($order < 1)
                $order = 1;

            $sql = "UPDATE category SET `order`=:order WHERE id = :id";
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

            $query = self::query('SELECT `order` FROM category WHERE id = :id'
                , array(':id'=>$id));
            $order = $query->fetchColumn(0);

            $order++;

            $sql = "UPDATE category SET `order`=:order WHERE id = :id";
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
            $query = self::query('SELECT MAX(`order`) FROM category');
            $order = $query->fetchColumn(0);
            return ++$order;

        }
    }
    
}