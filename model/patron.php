<?php
namespace Goteo\Model {

    use \Goteo\Library\Text,
        \Goteo\Model,
        \Goteo\Library\Check;

    class Patron extends \Goteo\Core\Model {

        public
            $id,
            $node,
            $project,
            $name,
            $user, // padrino
            $link,
            $order;

        /*
         *  Devuelve datos de un recomendado
         */
        public static function get ($project, $node = \GOTEO_NODE) {
                $query = static::query("
                    SELECT  
                        patron.id as id,
                        patron.node as node,
                        patron.project as project,
                        project.name as name,
                        patron.user as user,
                        patron.link as link,
                        patron.order as `order`
                    FROM    patron
                    LEFT JOIN patron_lang
                        ON patron_lang.id = patron.id
                        AND patron_lang.lang = :lang
                    INNER JOIN project
                        ON project.id = patron.project
                    WHERE patron.project = :project
                    AND patron.node = :node
                    ", array(':project'=>$project, ':node'=>$node, ':lang'=>\LANG));
                $patron = $query->fetchObject(__CLASS__);
                $patron->user = Model\User::getMini($patron->user);

                return $patron;
        }

        /*
         * Lista de proyectos recomendados
         */
        public static function getAll ($node = \GOTEO_NODE) {

            // estados
            $status = Model\Project::status();

            $promos = array();

            $query = static::query("
                SELECT
                    patron.id as id,
                    patron.project as project,
                    project.name as name,
                    project.status as status,
                    patron.user as user,
                    patron.link as link,
                    patron.order as `order`
                FROM    patron
                INNER JOIN project
                    ON project.id = patron.project
                WHERE patron.node = :node
                $sqlFilter
                ORDER BY `order` ASC, name ASC
                ", array(':node' => $node));
            
            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $promo) {
                $promo->user = Model\User::getMini($promo->user);
                $promo->status = $status[$promo->status];
                $promos[] = $promo;
            }

            return $promos;
        }

        /**
         * Devuelve los proyectos recomendados por un padrino para pintar un resultado de búsqueda
         *
         * @param varchar50 $user padrino
         */
        public function getList($user) {

            $projects = array();

            $values = array(':user'=>$user);

            $sql = "SELECT project FROM patron WHERE user = :user ORDER BY id DESC";
            $query = self::query($sql, $values);
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $proj) {
                $projects[] = Model\Project::getMedium($proj['project']);
            }

            return $projects;
        }

        /*
         * Lista de proyectos disponibles para recomendar
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
                WHERE status > 2
                AND project.id NOT IN (SELECT project FROM patron WHERE patron.node = :node{$sqlCurr} )
                ORDER BY name ASC
                ", array(':node' => $node));

            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }


        public function validate (&$errors = array()) { 
            if (empty($this->node))
                $errors[] = 'Falta nodo';

            if (empty($this->project))
                $errors[] = 'Falta proyecto';

            if (empty($this->user))
                $errors[] = 'Falta padrino';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'node',
                'project',
                'user',
                'link',
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
                $sql = "REPLACE INTO patron SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar un proyecto recomendado
         */
        public static function delete ($project, $user, $node = \GOTEO_NODE) {
            
            $sql = "DELETE FROM patron WHERE project = :project AND user = :user AND node = :node";
            if (self::query($sql, array(':project'=>$project, ':user'=>$user, ':node'=>$node))) {
                return true;
            } else {
                return false;
            }

        }

        /* Para activar/desactivar un recomendado
         */
        public static function setActive ($id, $active = false) {

            $sql = "UPDATE patron SET active = :active WHERE id = :id";
            if (self::query($sql, array(':id'=>$id, ':active'=>$active))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que un proyecto salga antes  (disminuir el order)
         */
        public static function up ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'up', 'patron', 'id', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'down', 'patron', 'id', 'order', $extra);
        }

        /*
         *
         */
        public static function next ($node = \GOTEO_NODE) {
            $query = self::query('SELECT MAX(`order`) FROM patron WHERE node = :node'
                , array(':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }


    }
    
}