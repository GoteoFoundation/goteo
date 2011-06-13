<?php

namespace Goteo\Model {

    use \Goteo\Model\Blog,
        \Goteo\Model\Project\Media,
        \Goteo\Model\Image;

    class Post extends \Goteo\Core\Model {

        public
            $id,
            $type,
            $owner,
            $project,
            $node,
            $posts = array();

        /*
         *  Devuelve datos de un blog
         */
        public static function get ($id) {
                $query = static::query("
                    SELECT
                        id,
                        title,
                        text,
                        `image`,
                        `media`
                    FROM    post
                    WHERE id = :id
                    ", array(':id' => $id));

                return $query->fetchObject(__CLASS__);
        }

        public function validate (&$errors = array()) {
            return true;
        }

        /*
         *  Para cuando se publica un proyecto o se crea un nodo
         */
        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'title',
                'text',
                'image',
                'media'
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

    }
    
}