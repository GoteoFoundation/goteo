<?php

namespace Goteo\Model {

    use \Goteo\Model\Project\Media,
        \Goteo\Model\Image;

    class Blog extends \Goteo\Core\Model {

        public
            $id,
            $type,
            $owner,
            $project,
            $node,
            $posts = array(),
            $active;

        /*
         *  Para conseguir el ide del blog de un proyecto o de un nodo
         *  Devuelve datos de un blog
         */
        public static function get ($owner, $type = 'project') {
                $query = static::query("
                    SELECT
                        id,
                        type,
                        owner,
                        active
                    FROM    blog
                    WHERE owner = :owner
                    AND type = :type
                    ", array(':owner' => $owner, ':type' => $type));
                
                $blog =  $query->fetchObject(__CLASS__);
                switch ($blog->type) {
                    case 'node':
                        $blog->node = $blog->owner;
                        break;
                    case 'project':
                        $blog->project = $blog->owner;
                        break;
                }
                if ($blog->node == \GOTEO_NODE) {
                    $blog->posts = Blog\Post::getAll();
                } elseif (!empty($blog->id)) {
                    $blog->posts = Blog\Post::getAll($blog->id);
                } else {
                    $blog->posts = array();
                }
                return $blog;
        }

        /*
         *  Listado simple de blogs de proyecto
         */
        public static function getListProj () {

            $list = array();

            $query = static::query("
                SELECT
                    blog.id as id,
                    project.name as name
                FROM    blog
                INNER JOIN post
                    ON post.blog = blog.id
                INNER JOIN project
                    ON project.id = blog.owner
                WHERE blog.type = 'project'
                ");

            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
        }

        /*
         *  Listado simple de blogs de nodo
         */
        public static function getListNode () {

            $list = array();

            $query = static::query("
                SELECT
                    blog.id as id,
                    node.name as name
                FROM    blog
                INNER JOIN post
                    ON post.blog = blog.id
                INNER JOIN node
                    ON node.id = blog.owner
                WHERE blog.type = 'node'
                ");

            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $item) {
                $list[$item->id] = $item->name;
            }

            return $list;
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
                'type',
                'owner',
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
                $sql = "REPLACE INTO blog SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         *  Para saber si un proyecto tiene novedades publicadas
         */
        public static function hasUpdates ($project) {
                $query = static::query("
                    SELECT
                        COUNT(post.id) as num
                    FROM blog
                    LEFT JOIN post
                    ON post.blog = blog.id
                    WHERE post.publish = 1
                    AND blog.type = 'project'
                    AND blog.owner = :id
                    GROUP BY post.blog
                    ", array(':id' => $project));

                $post = $query->fetchObject(__CLASS__);
                $num = $post->fetchColumn(0);
                return ($num > 0);
        }
        
    }
    
}