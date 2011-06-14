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

                return $query->fetchObject(__CLASS__);
        }

        /*
         * Lista de entradas
         */
        public static function getAll ($blog = 1) {

            $list = array();

            $sql = "
                SELECT
                    id,
                    title,
                    `text`,
                    blog,
                    `media`,
                    `order`
                FROM    post
                WHERE   blog = $blog
                AND     home = 1
                ORDER BY `order` ASC, title ASC
                ";
            
            $query = static::query($sql);
                
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $post) {
                $post->media = new Media($post->media);

                $list[] = $post;
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
        public static function up ($id) {
            $extra = array (
                    'home' => 1,
                    'blog' => 1
                );
            return Check::reorder($id, 'up', 'post', 'id', 'order', $extra);
        }

        /*
         * Para que un proyecto salga despues  (aumentar el order)
         */
        //@FIXME essse blog a piñon!
        public static function down ($id) {
            $extra = array (
                    'home' => 1,
                    'blog' => 1
                );
            return Check::reorder($id, 'down', 'post', 'id', 'order', $extra);
        }

        /*
         * Orden para añadirlo al final
         */
        public static function next () {
            $query = self::query('SELECT MAX(`order`) FROM post WHERE home=1'
                , array(':media'=>$media, ':node'=>$node));
            $order = $query->fetchColumn(0);
            return ++$order;

        }

    }
    
}