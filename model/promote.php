<?php
namespace Goteo\Model {

    use \Goteo\Library\Text,
        \Goteo\Model\Project,
        \Goteo\Library\Check;

    class Promote extends \Goteo\Core\Model {

        public
            $id,
            $node,
            $project,
            $name,
            $title,
            $description,
            $order,
            $active;

        /*
         *  Devuelve datos de un destacado
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT  
                        promote.id as id,
                        promote.node as node,
                        promote.project as project,
                        project.name as name,
                        IFNULL(promote_lang.title, promote.title) as title,
                        IFNULL(promote_lang.description, promote.description) as description,
                        promote.order as `order`,
                        promote.active as `active`
                    FROM    promote
                    LEFT JOIN promote_lang
                        ON promote_lang.id = promote.id
                        AND promote_lang.lang = :lang
                    INNER JOIN project
                        ON project.id = promote.project
                    WHERE promote.id = :id
                    ", array(':id'=>$id, ':lang'=>\LANG));
                $promote = $query->fetchObject(__CLASS__);

                return $promote;
        }

        /*
         * Lista de proyectos destacados
         */
        public static function getAll ($activeonly = false, $node = \GOTEO_NODE, $lang = \LANG) {

            // estados
            $status = Project::status();

            $promos = array();

            $sqlFilter = ($activeonly) ? " AND promote.active = 1" : '';

            $query = static::query("
                SELECT
                    promote.id as id,
                    promote.project as project,
                    project.name as name,
                    project.status as status,
                    IFNULL(promote_lang.title, promote.title) as title,
                    IFNULL(promote_lang.description, promote.description) as description,
                    promote.order as `order`,
                    promote.active as `active`
                FROM    promote
                LEFT JOIN promote_lang
                    ON promote_lang.id = promote.id
                    AND promote_lang.lang = :lang
                INNER JOIN project
                    ON project.id = promote.project
                WHERE promote.node = :node
                $sqlFilter
                ORDER BY `order` ASC, title ASC
                ", array(':node' => $node, ':lang'=>$lang));
            
            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $promo) {
                $promo->description =Text::recorta($promo->description, 100, false);
                $promo->status = $status[$promo->status];
                $promos[] = $promo;
            }

            return $promos;
        }

        /*
         * Lista de proyectos disponibles para destacar
         */
        public static function available ($current = null, $node = \GOTEO_NODE) {

            if (!empty($current)) {
                $sqlCurr = " AND project != '$current'";
            } else {
                $sqlCurr = "";
            }

            $query = static::query("
                SELECT
                    project.id as id,
                    project.name as name,
                    project.status as status
                FROM    project
                WHERE status = 3
                AND project.id NOT IN (SELECT project FROM promote WHERE promote.node = :node{$sqlCurr} )
                ORDER BY name ASC
                ", array(':node' => $node));

            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }

        // ya no validamos esto
        public function validate (&$errors = array()) { 
            if (empty($this->node))
                $errors[] = 'Falta nodo';

            if ($this->active && empty($this->project))
                $errors[] = 'Se muestra y no tiene proyecto';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
//            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'node',
                'project',
                'title',
                'description',
                'order',
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
                $sql = "REPLACE INTO promote SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar un proyecto destacado
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM promote WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /* Para activar/desactivar un destacado
         */
        public static function setActive ($id, $active = false) {

            $sql = "UPDATE promote SET active = :active WHERE id = :id";
            if (self::query($sql, array(':id'=>$id, ':active'=>$active))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que salga antes  (disminuir el order)
         */
        public static function up ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'up', 'promote', 'id', 'order', $extra);
        }

        /*
         * Para que salga despues  (aumentar el order)
         */
        public static function down ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'down', 'promote', 'id', 'order', $extra);
        }

        /*
         *
         */
        public static function next ($node = \GOTEO_NODE) {
            $query = self::query('SELECT MAX(`order`) FROM promote WHERE node = :node'
                , array(':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }


    }
    
}