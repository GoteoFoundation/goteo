<?php
namespace Goteo\Model {

    use Goteo\Library\Text,
        Goteo\Model\Project,
        Goteo\Model\Image,
        Goteo\Library\Check;

    class Banner extends \Goteo\Core\Model {

        public
            $id,
            $node,
            $project,
            $image,
            $order;

        /*
         *  Devuelve datos de un banner de proyecto
         */
        public static function get ($id, $lang = null) {
                $query = static::query("
                    SELECT  
                        banner.id as id,
                        banner.node as node,
                        banner.project as project,
                        project.name as name,
                        IFNULL(banner_lang.title, banner.title) as title,
                        IFNULL(banner_lang.description, banner.description) as description,
                        banner.url as url,
                        banner.image as image,
                        banner.order as `order`,
                        banner.active as `active`
                    FROM    banner
                    LEFT JOIN banner_lang
                        ON  banner_lang.id = banner.id
                        AND banner_lang.lang = :lang
                    LEFT JOIN project
                        ON project.id = banner.project
                    WHERE banner.id = :id
                    ", array(':id'=>$id, ':lang' => $lang));
                $banner = $query->fetchObject(__CLASS__);

                $banner->image = Image::get($banner->image);



                return $banner;
        }

        /*
         * Lista de proyectos en banners
         */
        public static function getAll ($activeonly = false, $node = \GOTEO_NODE) {

            // estados
            $status = Project::status();

            $banners = array();

            $sqlFilter = ($activeonly) ? " AND banner.active = 1" : '';

            $query = static::query("
                SELECT
                    banner.id as id,
                    banner.node as node,
                    banner.project as project,
                    project.name as name,
                    IFNULL(banner_lang.title, banner.title) as title,
                    IFNULL(banner_lang.description, banner.description) as description,
                    banner.url as url,
                    project.status as status,
                    banner.image as image,
                    banner.order as `order`,
                    banner.active as `active`
                FROM    banner
                LEFT JOIN project
                    ON project.id = banner.project
                LEFT JOIN banner_lang
                    ON  banner_lang.id = banner.id
                    AND banner_lang.lang = :lang
                WHERE banner.node = :node
                $sqlFilter
                ORDER BY `order` ASC
                ", array(':node' => $node, ':lang' => \LANG));
            
            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $banner) {
                $banner->image = !empty($banner->image) ? Image::get($banner->image) : null;
                $banner->status = $status[$banner->status];
                $banners[] = $banner;
            }

            return $banners;
        }

        /*
         * Lista de banners
         */
        public static function getList ($node = \GOTEO_NODE) {

            $banners = array();
            // solo banenrs de nodo
            if ($node == \GOTEO_NODE) {
                return false;
            }

            $query = static::query("
                SELECT
                    banner.id as id,
                    banner.node as node,
                    banner.title as title,
                    banner.description as description
                FROM    banner
                WHERE banner.node = :node
                ORDER BY `order` ASC
                ", array(':node' => $node));

            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $banner) {
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

        // ya no validamos esto,  puede haber banners in proyecto y sin imagen
        public function validate (&$errors = array()) {
            if (empty($this->project))
                $errors[] = 'Falta proyecto';

            if (empty($this->image))
                $errors[] = 'Falta imagen';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
//            if (!$this->validate($errors)) return false;

            // Imagen de fondo de banner
            if (is_array($this->image) && !empty($this->image['name'])) {
                $image = new Image($this->image);
                if ($image->save()) {
                    $this->image = $image->id;
                } else {
                    \Goteo\Library\Message::Error(Text::get('image-upload-fail') . implode(', ', $errors));
                    $this->image = '';
                }
            }

            $fields = array(
                'id',
                'node',
                'title',
                'description',
                'url',
                'project',
                'image',
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
                $sql = "REPLACE INTO banner SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar un proyecto banner
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM banner WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /* Para activar/desactivar un banner
         */
        public static function setActive ($id, $active = false) {

            $sql = "UPDATE banner SET active = :active WHERE id = :id";
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
            return Check::reorder($id, 'up', 'banner', 'id', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        public static function down ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'down', 'banner', 'id', 'order', $extra);
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