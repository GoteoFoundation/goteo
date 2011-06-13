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
                    date,
                    text,
                    user
                FROM    comment
                ORDER BY `date` DESC, id DESC
                ";
            
            $query = static::query($sql);
                
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $comment) {
                $list[$comment->id] = $comment;
            }

            return $list;
        }

        public function validate (&$errors = array()) { 
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