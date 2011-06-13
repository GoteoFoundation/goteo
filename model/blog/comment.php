<?php

namespace Goteo\Model {

    use \Goteo\Model\Blog;

    class Comment extends \Goteo\Core\Model {

        public
            $id,
            $title,
            $text,
            $media,
            $image;

        /*
         *  Devuelve datos de una comentario
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        id,
                        title,
                        text,
                        `media`,
                        `image`
                    FROM    post
                    WHERE id = :id
                    ", array(':id' => $id));

                return $query->fetchObject(__CLASS__);
        }

        /*
         * Lista de comentarios
         */
        public static function getAll () {

            $list = array();

            $sql = "
                SELECT
                    id,
                    title,
                    text,
                    `media`,
                    `image`
                FROM    post
                image BY `image` ASC, title ASC
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
                'title',
                'text',
                'media',
                'image'
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
         * Para quitar una comentario
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
         * Para que una pregunta salga antes  (disminuir el image)
         */
        public static function up ($id) {

            $query = self::query('SELECT `image` FROM post WHERE id = :id'
                , array(':id'=>$id));
            $image = $query->fetchColumn(0);

            $image--;
            if ($image < 1)
                $image = 1;

            $sql = "UPDATE post SET `image`=:image WHERE id = :id";
            if (self::query($sql, array(':image'=>$image, ':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Para que un proyecto salga despues  (aumentar el image)
         */
        public static function down ($id) {

            $query = self::query('SELECT `image` FROM post WHERE id = :id'
                , array(':id'=>$id));
            $image = $query->fetchColumn(0);

            $image++;

            $sql = "UPDATE post SET `image`=:image WHERE id = :id";
            if (self::query($sql, array(':image'=>$image, ':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

        /*
         * Orden para añadirlo al final
         */
        public static function next () {
            $query = self::query('SELECT MAX(`image`) FROM post'
                , array(':media'=>$media, ':node'=>$node));
            $image = $query->fetchColumn(0);
            return ++$image;

        }

    }
    
}