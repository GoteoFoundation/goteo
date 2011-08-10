<?php
namespace Goteo\Model {

    use \Goteo\Library\Text,
        \Goteo\Model\Project,
        \Goteo\Library\Check;

    class Promote extends \Goteo\Core\Model {

        public
            $node,
            $project,
            $name,
            $title,
            $description,
            $order;

        /*
         *  Devuelve datos de un destacado
         */
        public static function get ($project, $node = \GOTEO_NODE) {
                $query = static::query("
                    SELECT  
                        promote.node as node,
                        promote.project as project,
                        project.name as name,
                        IFNULL(promote_lang.title, promote.title) as title,
                        IFNULL(promote_lang.description, promote.description) as description,
                        promote.order as `order`
                    FROM    promote
                    LEFT JOIN promote_lang
                        ON promote_lang.id = promote.id
                        AND promote_lang.lang = :lang
                    INNER JOIN project
                        ON project.id = promote.project
                    WHERE promote.project = :project
                    AND promote.node = :node
                    ", array(':project'=>$project, ':node'=>$node, ':lang'=>\LANG));
                $promote = $query->fetchObject(__CLASS__);

                return $promote;
        }

        /*
         * Lista de proyectos destacados
         */
        public static function getAll ($node = \GOTEO_NODE) {

            // estados
            $status = Project::status();

            $promos = array();

            $query = static::query("
                SELECT
                    promote.project as project,
                    project.name as name,
                    project.status as status,
                    IFNULL(promote_lang.title, promote.title) as title,
                    IFNULL(promote_lang.description, promote.description) as description,
                    promote.order as `order`
                FROM    promote
                LEFT JOIN promote_lang
                    ON promote_lang.id = promote.id
                    AND promote_lang.lang = :lang
                INNER JOIN project
                    ON project.id = promote.project
                WHERE promote.node = :node
                ORDER BY `order` ASC, title ASC
                ", array(':node' => $node, ':lang'=>\LANG));
            
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
        public static function available ($node = \GOTEO_NODE) {

            $query = static::query("
                SELECT
                    project.id as id,
                    project.name as name,
                    project.status as status
                FROM    project
                WHERE status > 2
                AND status < 6
                AND project.id NOT IN (SELECT project FROM promote WHERE promote.node = :node)
                ORDER BY name ASC
                ", array(':node' => $node));

            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }


        public function validate (&$errors = array()) { 
            if (empty($this->node))
                $errors[] = 'Falta nodo';
                //Text::get('mandatory-promote-node');

            if (empty($this->project))
                $errors[] = 'Falta proyecto';
                //Text::get('validate-promote-noproject');

            if (empty($this->title))
                $errors[] = 'Falta título';
                //Text::get('mandatory-promote-title');

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'node',
                'project',
                'title',
                'description',
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
                $sql = "REPLACE INTO promote SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar un proyecto destacado
         */
        public static function delete ($project, $node = \GOTEO_NODE) {
            
            $sql = "DELETE FROM promote WHERE project = :project AND node = :node";
            if (self::query($sql, array(':project'=>$project, ':node'=>$node))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que un proyecto salga antes  (disminuir el order)
         */
        public static function up ($project, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($project, 'up', 'promote', 'project', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($project, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($project, 'down', 'promote', 'project', 'order', $extra);
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