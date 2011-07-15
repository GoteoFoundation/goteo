<?php

namespace Goteo\Model {

    use Goteo\Model\Project\Media,
        Goteo\Library\Check;

    class Post extends \Goteo\Core\Model {

        public
            $id,
            $title,
            $text,
            $media,
            $order;

        /*
         *  Devuelve datos de una entrada
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        id,
                        title,
                        `text`,
                        blog,
                        image,
                        `media`,
                        `order`
                    FROM    post
                    WHERE id = :id
                    ", array(':id' => $id));

                $post = $query->fetchObject(__CLASS__);
                
                if (!empty($post->image)) {
                    $post->image = Image::get($post->image);
                }
                $post->media = new Media($post->media);

                return $post;

        }

        /*
         * Lista de entradas
         */
        public static function getAll ($position = 'home', $blog = 1) {

            if (!in_array($position, array('home', 'footer'))) {
                $position = 'home';
            }

            $list = array();

            $sql = "
                SELECT
                    id,
                    title,
                    `text`,
                    blog,
                    `media`,
                    image,
                    `order`,
                    `home`,
                    `footer`
                FROM    post
                WHERE   blog = $blog
                AND     $position = 1
                ORDER BY `order` ASC, title ASC
                ";
            
            $query = static::query($sql);
                
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $post) {
                $post->media = new Media($post->media);

                // imagen
                if (!empty($post->image)) {
                    $post->image = Image::get($post->image);
                }

                $post->type = $post->home == 1 ? 'home' : 'footer';

                $list[$post->id] = $post;
            }

            return $list;
        }

        /*
         * Entradas en portada o pie
         */
        public static function getList ($position = 'home', $blog = 1) {

            if (!in_array($position, array('home', 'footer'))) {
                $position = 'home';
            }

            $list = array();

            $sql = "
                SELECT
                    id,
                    title,
                    `order`
                FROM    post
                WHERE   blog = $blog
                AND     $position = 1
                ORDER BY `order` ASC, title ASC
                ";

            $query = static::query($sql);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $post) {
                $list[$post->id] = $post->title;
            }

            return $list;
        }

        public function validate (&$errors = array()) { 
            if (empty($this->title))
                $errors[] = 'Falta título';
                //Text::get('mandatory-post-title');

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'blog',
                'title',
                'text',
                'media',
                'order',
                'home',
                'footer'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO post SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "No se ha guardado correctamente. " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar una entrada
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM post WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que una pregunta salga antes  (disminuir el order)
         */
        //@FIXME essse blog a piñon!
        public static function up ($id, $type = 'home') {
            $extra = array (
                    $type => 1,
                    'blog' => 1
                );
            return Check::reorder($id, 'up', 'post', 'id', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        //@FIXME essse blog a piñon!
        public static function down ($id, $type = 'home') {
            $extra = array (
                    $type => 1,
                    'blog' => 1
                );
            return Check::reorder($id, 'down', 'post', 'id', 'order', $extra);
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next ($type = 'home') {
            $query = self::query('SELECT MAX(`order`) FROM post WHERE '.$type.'=1'
                , array(':media'=>$media, ':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

    }
    
}