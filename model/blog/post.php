<?php

namespace Goteo\Model\Blog {

    use \Goteo\Model\Project\Media,
        \Goteo\Model\Image,
        \Goteo\Library\Text;

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
                        `date`,
                        DATE_FORMAT(date, '%d | %m | %Y') as fecha,
                        home
                    FROM    post
                    WHERE id = :id
                    ", array(':id' => $id));

                $post = $query->fetchObject(__CLASS__);

                // imagen
                if (!empty($post->image)) {
                    $post->image = Image::get($post->image);
                }

                // video
                if (isset($post->media)) {
                    $post->media = new Media($post->media);
                }


                $post->comments = Post\Comment::getAll($id);
                $post->num_comments = count($post->comments);

                //tags
                $post->tags = Post\Tag::getAll($id);

                // reconocimiento de enlaces y saltos de linea
                $post->text = nl2br(Text::urlink($post->text));

                return $post;
        }

        /*
         * Lista de entradas
         * de mas nueva a mas antigua
         * // si es portada son los que se meten por la gestion de entradas en portada que llevan el tag 1 'Portada'
         */
        public static function getAll ($blog, $limit = null) {

            $list = array();

            $sql = "
                SELECT
                    id,
                    blog,
                    title,
                    text,
                    `image`,
                    `media`,
                    DATE_FORMAT(date, '%d-%m-%Y') as date,
                    DATE_FORMAT(date, '%d | %m | %Y') as fecha,
                    home
                FROM    post
                WHERE blog = ?
                ORDER BY date DESC, id DESC
                ";
            if (!empty($limit)) {
                $sql .= "LIMIT $limit";
            }
            
            $query = static::query($sql, array($blog));
                
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $post) {
                // imagen
                if (!empty($post->image)) {
                    $post->image = Image::get($post->image);
                }

                // video
                if (isset($post->media)) {
                    $post->media = new Media($post->media);
                }
                
                $post->num_comments = Post\Comment::getCount($post->id);

                // reconocimiento de enlaces y saltos de linea
                $post->text = nl2br(Text::urlink($post->text));

                $list[$post->id] = $post;
            }

            return $list;
        }

        /*
         * Lista de entradas filtradas por tag
         * de mas nueva a mas antigua
         */
        public static function getList ($blog, $tag) {

            $list = array();

            $sql = "
                SELECT
                    id,
                    blog,
                    title,
                    text,
                    `image`,
                    `media`,
                    DATE_FORMAT(date, '%d-%m-%Y') as date,
                    DATE_FORMAT(date, '%d-%m-%Y') as fecha,
                    home
                FROM    post
                INNER JOIN post_tag
                    ON post_tag.post = post.id
                    AND post_tag.tag = :tag
                WHERE blog = :blog
                ORDER BY date DESC, id DESC
                ";

            $query = static::query($sql, array(':blog'=>$blog, ':tag'=>$tag));

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $post) {
                // imagen
                if (!empty($post->image)) {
                    $post->image = Image::get($post->image);
                }

                // video
                if (isset($post->media)) {
                    $post->media = new Media($post->media);
                }

                $post->num_comments = Post\Comment::getCount($post->id);

                $list[$post->id] = $post;
            }

            return $list;
        }

        public function validate (&$errors = array()) { 
            if (empty($this->title))
                $errors['title'] = 'Falta título';

            if (empty($this->text))
                $errors['text'] = 'Falta texto';

            if (empty($this->date))
                $errors['date'] = 'Falta fecha';

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            // Primero la imagenImagen
            if (is_array($this->image) && !empty($this->image['name'])) {
                $image = new Image($this->image);
                if ($image->save($errors)) {
                    $this->image = $image->id;
                } else {
                    $this->image = '';
                }
            }

            $fields = array(
                'id',
                'blog',
                'title',
                'text',
                'image',
                'media',
                'date',
                'allow',
                'home'
                );

            // si editan por aqui no salen en portada, por ahora
            //@FIXME
            $set = '`order` = 0 ';
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

                // y los tags, si hay
                if (!empty($this->id) && is_array($this->tags)) {
                    static::query('DELETE FROM post_tag WHERE post= ?', $this->id);
                    foreach ($this->tags as $tag) {
                        $new = new Post\Tag(
                                array(
                                    'post' => $this->id,
                                    'tag' => $tag
                                )
                            );
                        $new->assign($errors);
                        unset($new);
                    }
                }

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
         *  Para saber si una entrada permite comentarios
         */
        public static function allowed ($id) {
                $query = static::query("
                    SELECT
                        allow
                    FROM    post
                    WHERE id = :id
                    ", array(':id' => $id));

                $post = $query->fetchObject(__CLASS__);

                if ($post->allow > 0) {
                    return true;
                } else {
                    return false;
                }
        }

    }
    
}