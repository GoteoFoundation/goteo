<?php
namespace Goteo\Model {

    use Goteo\Library\Text,
        Goteo\Model\Project,
        Goteo\Model\Image,
        Goteo\Library\Check;

    class Stories extends \Goteo\Core\Model {

        public
            $id,
            $node,
            $project,
            $order,
            $image,
            $title,
            $description,
            $review,
            $url,
            $post;

        /*
         *  Devuelve datos de una historia exitosa
         */
        public static function get ($id, $lang = null) {
                $query = static::query("
                    SELECT
                        stories.id as id,
                        stories.node as node,
                        stories.project as project,
                        project.name as name,
                        IFNULL(stories_lang.title, stories.title) as title,
                        IFNULL(stories_lang.description, stories.description) as description,
                        IFNULL(stories_lang.review, stories.review) as review,
                        stories.url as url,
                        stories.image as image,
                        stories.order as `order`,
                        stories.post as `post`,
                        stories.active as `active`
                    FROM    stories
                    LEFT JOIN stories_lang
                        ON  stories_lang.id = stories.id
                        AND stories_lang.lang = :lang
                    LEFT JOIN project
                        ON project.id = stories.project
                    WHERE stories.id = :id
                    ", array(':id'=>$id, ':lang' => $lang));
                $story = $query->fetchObject(__CLASS__);

                $story->image = Image::get($story->image);

                return $story;
        }

        /*
         * Lista de historias exitosas
         */
        public static function getAll ($activeonly = false, $node = \GOTEO_NODE) {

            // estados
            $status = Project::status();

            $stories = array();

            $sqlFilter = ($activeonly) ? " AND stories.active = 1" : '';

            $query = static::query("
                SELECT
                    stories.id as id,
                    stories.node as node,
                    stories.project as project,
                    project.name as name,
                    IFNULL(stories_lang.title, stories.title) as title,
                    IFNULL(stories_lang.description, stories.description) as description,
                    IFNULL(stories_lang.review, stories.review) as review,
                    stories.url as url,
                    project.status as status,
                    stories.image as image,
                    stories.order as `order`,
                    stories.post as `post`,
                    stories.active as `active`
                FROM    stories
                LEFT JOIN project
                    ON project.id = stories.project
                LEFT JOIN stories_lang
                    ON  stories_lang.id = stories.id
                    AND stories_lang.lang = :lang
                WHERE stories.node = :node
                $sqlFilter
                ORDER BY `order` ASC
                ", array(':node' => $node, ':lang' => \LANG));

            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $story) {
                $story->image = !empty($story->image) ? Image::get($story->image) : null;
                $story->status = $status[$story->status];
                $stories[] = $story;
            }

            return $stories;
        }

        /*
         * Lista de historias exitosas
         */
        public static function getList ($node = \GOTEO_NODE) {

            $stories = array();

            $query = static::query("
                SELECT
                    stories.id as id,
                    stories.node as node,
                    stories.title as title,
                    stories.description as description,
                    stories.review as review
                FROM    stories
                WHERE stories.node = :node
                ORDER BY `order` ASC
                ", array(':node' => $node));

            foreach($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $story) {
                $stories[] = $story;
            }

            return $stories;
        }

        /*
         * Lista de proyectos disponibles para destacar
         */
        public static function available ($current = null, $node = \GOTEO_NODE) {

            if (!empty($current)) {
                $sqlCurr = " AND stories.project != '$current'";
            } else {
                $sqlCurr = "";
            }

            $query = static::query("
                SELECT
                    project.id as id,
                    project.name as name,
                    project.status as status
                FROM    project
                WHERE status = 5
                AND project.id NOT IN (SELECT project FROM stories WHERE stories.node = :node AND project IS NOT NULL {$sqlCurr} )
                ORDER BY name ASC
                ", array(':node' => $node));

            return $query->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        }

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
            if (!$this->validate($errors)) return false;

            // Imagen de fondo de stories
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
                'project',
                'order',
                'image',
                'active',
                'title',
                'description',
                'review',
                'url',
                'post'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO stories SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una historia exitosa
         */
        public static function delete ($id) {

            $sql = "DELETE FROM stories WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /* Para activar/desactivar una historia exitosa
         */
        public static function setActive ($id, $active = false) {

            $sql = "UPDATE stories SET active = :active WHERE id = :id";
            if (self::query($sql, array(':id'=>$id, ':active'=>$active))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que una historia salga antes (disminuir el order)
         */
        public static function up ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'up', 'stories', 'id', 'order', $extra);
        }

        /*
         * Para que una historia salga después (aumentar el order)
         */
        public static function down ($id, $node = \GOTEO_NODE) {
            $extra = array (
                    'node' => $node
                );
            return Check::reorder($id, 'down', 'stories', 'id', 'order', $extra);
        }

        /*
         *
         */
        public static function next ($node = \GOTEO_NODE) {
            $query = self::query('SELECT MAX(`order`) FROM stories WHERE node = :node'
                , array(':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }


    }

}
