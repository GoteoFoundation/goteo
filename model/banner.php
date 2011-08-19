<?php
namespace Goteo\Model {

    use \Goteo\Library\Text,
        \Goteo\Model\Project,
        \Goteo\Library\Check;

    class Banner extends \Goteo\Core\Model {

        public
            $node,
            $project,
            $name,
            $title,
            $description,
            $order;

        /*
         *  Devuelve datos de un banner de proyecto
         */
        public static function get ($project, $node = \GOTEO_NODE) {
                $query = static::query("
                    SELECT  
                        banner.node as node,
                        banner.project as project,
                        project.name as name,
                        banner.order as `order`
                    FROM    banner
                    INNER JOIN project
                        ON project.id = banner.project
                    WHERE banner.project = :project
                    AND banner.node = :node
                    ", array(':project'=>$project, ':node'=>$node));
                $banner = $query->fetchObject(__CLASS__);

                return $banner;
        }

        /*
         * Lista de proyectos en banners
         */
        public static function getAll ($node = \GOTEO_NODE) {

            // estados
            $status = Project::status();

            $banners = array();

            $query = static::query("
                SELECT
                    banner.project as project,
                    project.name as name,
                    project.status as status,
                    banner.order as `order`
                FROM    banner
                INNER JOIN project
                    ON project.id = banner.project
                WHERE banner.node = :node
                ORDER BY `order` ASC
                ", array(':node' => $node));
            
            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $banner) {
                $banner->status = $status[$banner->status];
                $banners[] = $banner;
            }

            return $banners;
        }

        /*
         * Lista de proyectos disponibles para destacar
         */
        public static function available ($current = null, $node = \GOTEO_NODE) {

            if (!empty($current)) {
                $sqlCurr = " AND banner.project != '$current'";
            } else {
                $sqlCurr = "";
            }

            $query = static::query("
                SELECT
                    project.id as id,
                    project.name as name,
                    project.status as status
                FROM    project
                WHERE status > 2
                AND status < 6
                AND project.id NOT IN (SELECT project FROM banner WHERE banner.node = :node{$sqlCurr} )
                ORDER BY name ASC
                ", array(':node' => $node));

            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }


        public function validate (&$errors = array()) { 
            if (empty($this->node))
                $errors[] = 'Falta nodo';
                //Text::get('mandatory-banner-node');

            if (empty($this->project))
                $errors[] = 'Falta proyecto';
                //Text::get('validate-banner-noproject');

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
                $sql = "REPLACE INTO banner SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar un proyecto banner
         */
        public static function delete ($project, $node = \GOTEO_NODE) {
            
            $sql = "DELETE FROM banner WHERE project = :project AND node = :node";
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
            return Check::reorder($project, 'up', 'banner', 'project', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($project, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($project, 'down', 'banner', 'project', 'order', $extra);
        }

        /*
         *
         */
        public static function next ($node = \GOTEO_NODE) {
            $query = self::query('SELECT MAX(`order`) FROM banner WHERE node = :node'
                , array(':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }


    }
    
}