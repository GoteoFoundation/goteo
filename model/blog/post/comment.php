<?php

namespace Goteo\Model\Blog\Post {

    class Comment extends \Goteo\Core\Model {

        public
            $id,
            $post,
            $date,
            $text,
            $user;

        /*
         *  Devuelve datos de una comentario
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        id,
                        post,
                        date,
                        text,
                        user
                    FROM    comment
                    WHERE id = :id
                    ", array(':id' => $id));

                return $query->fetchObject(__CLASS__);
        }

        /*
         * Lista de comentarios
         */
        public static function getAll ($post) {

            $list = array();

            $sql = "
                SELECT
                    id,
                    post,
                    DATE_FORMAT(date, '%d/%m/%Y') as date,
                    text,
                    user
                FROM    comment
                WHERE post = ?
                ORDER BY `date` DESC, id DESC
                ";
            
            $query = static::query($sql, array($post));
                
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $comment) {
                $comment->user = \Goteo\Model\User::getMini($comment->user);
                $list[$comment->id] = $comment;
            }

            return $list;
        }

        /*
         * Lista de comentarios en el blog
         */
        public static function getList($blog, $limit = null) {

            $list = array();

            $sql = "
                SELECT
                    id,
                    post,
                    DATE_FORMAT(date, '%d/%m/%Y') as date,
                    text,
                    user
                FROM    comment
                WHERE post IN (SELECT id FROM post WHERE blog = ?)
                ORDER BY `date` DESC, id DESC
                ";
            if (!empty($limit)) {
                $sql .= "LIMIT $limit";
            }

            $query = static::query($sql, array($blog));

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $comment) {
                $comment->user = \Goteo\Model\User::getMini($comment->user);
                $list[$comment->id] = $comment;
            }

            return $list;
        }

        /*
         *  Devuelve cuantos comentarios tiene una entrada
         */
        public static function getCount ($post) {
                $query = static::query("
                    SELECT
                        COUNT(id) as cuantos
                    FROM    comment
                    WHERE post = :post
                    ", array(':post' => $post));

                $count = $query->fetchObject();

                return (int) $count->cuantos;
        }

        public function validate (&$errors = array()) { 
            if (empty($this->text))
                $errors[] = 'Falta texto';
                //Text::get('mandatory-comment-text');

            if (empty($errors))
                return true;
            else
                return false;
        }

        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'post',
                'date',
                'text',
                'user'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO comment SET " . $set;
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
            
            $sql = "DELETE FROM comment WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
                return false;
            }

        }

    }
    
}