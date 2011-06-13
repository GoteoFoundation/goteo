<?php

namespace Goteo\Model\Blog {

    use \Goteo\Model\Project\Media,
        \Goteo\Model\Image;

    class Post extends \Goteo\Core\Model {

        public
            $id,
            $blog,
            $title,
            $text,
            $image,
            $media,
            $date,
            $num_comments = 0,
            $comments = array();

        /*
         *  Devuelve datos de una entrada
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        id,
                        blog,
                        title,
                        text,
                        `image`,
                        `media`,
                        `date`
                    FROM    post
                    WHERE id = :id
                    ", array(':id' => $id));

                $post = $query->fetchObject(__CLASS__);

                $post->comments = Post\Comment::getAll($id);

                return $post;
        }

        /*
         * Lista de entradas
         * de mas nueva a mas antigua
         * // si es portada son los que se meten por la gestion de entradas en portada que llevan el tag 1 'Portada'
         */
        public static function getAll ($blog, $portada = false) {

            $list = array();

            $sql = "
                SELECT
                    id,
                    blog,
                    title,
                    text,
                    `image`,
                    `media`,
                    DATE_FORMAT(date, '%d-%m-%Y') as date
                FROM    post
                ORDER BY date DESC, id DESC
                ";
            
            $query = static::query($sql);
                
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $post) {
                // el video
                $post->media = new Media($post->media);
                $post->iamge = Image::get($post->image);
                $post->num_comments = Post\Comment::getCount($post->id);

                $list[$post->id] = $post;
            }

            return $list;
        }

        public function validate (&$errors = array()) { 
            if (empty($this->title))
                $errors[] = 'Falta título';

            if (empty($this->text))
                $errors[] = 'Falta texto';

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
                'image',
                'media',
                `date`
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

    }
    
}