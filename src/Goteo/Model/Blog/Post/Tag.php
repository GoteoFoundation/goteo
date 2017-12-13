<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Blog\Post {

    use Goteo\Application\Lang;
    use Goteo\Application\Config;

    class Tag extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $post,
            $tag;


        /*
         *  Devuelve datos de una comentario
         */
        public static function get ($id, $lang = null) {

                //Obtenemos el idioma de soporte
                $lang=self::default_lang_by_id($id, 'tag_lang', $lang);
                $query = static::query("
                    SELECT
                        tag.id as id,
                        IFNULL(tag_lang.name, tag.name) as name
                    FROM    tag
                    LEFT JOIN tag_lang
                        ON  tag_lang.id = tag.id
                        AND tag_lang.lang = :lang
                    WHERE tag.id = :id
                    ", array(':id' => $id, ':lang'=>$lang));

                return $query->fetchObject(__CLASS__);
        }

        /*
         * Lista de tags
         * de un post si recibe el parametro
         */
        public static function getAll ($post = null) {
            $lang = Lang::current();
            $list = array();

            $values = array(':lang'=>$lang);

            if(self::default_lang($lang) === Config::get('lang')) {
                $different_select=" IFNULL(tag_lang.name, tag.name) as name";
                }
            else {
                $different_select=" IFNULL(tag_lang.name, IFNULL(eng.name, tag.name)) as name";

                $eng_join=" LEFT JOIN tag_lang as eng
                                ON  eng.id = tag.id
                                AND eng.lang = 'en'";
                }

            $sql = "
                SELECT
                    tag.id as id,
                    $different_select
                FROM    tag
                LEFT JOIN tag_lang
                    ON  tag_lang.id = tag.id
                    AND tag_lang.lang = :lang
                $eng_join
                ";

            if (!empty($post)) {
                $sql .= "INNER JOIN post_tag
                    ON tag.id = post_tag.tag
                    AND post_tag.post = :post
                    ";
                $values[':post'] = $post;
            }

            $sql .= "ORDER BY tag.name ASC";

            $query = static::query($sql, $values);

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $tag) {
                $list[$tag->id] = $tag->name;
            }

            return $list;
        }

        /*
         * Lista simple de tags
         */
        public static function getList () {
            $lang = Lang::current();
            $list = array();

            if(self::default_lang($lang) === Config::get('lang')) {
                $different_select=" IFNULL(tag_lang.name, tag.name) as name";
                }
            else {
                $different_select=" IFNULL(tag_lang.name, IFNULL(eng.name, tag.name)) as name";

                $eng_join=" LEFT JOIN tag_lang as eng
                                ON  eng.id = tag.id
                                AND eng.lang = 'en'";
                }

            $sql = "
                SELECT
                    tag.id as id,
                    $different_select,
                    (   SELECT
                        COUNT(post_tag.post)
                        FROM post_tag
                        WHERE post_tag.tag = tag.id
                    ) as used
                FROM    tag
                LEFT JOIN tag_lang
                    ON  tag_lang.id = tag.id
                    AND tag_lang.lang = :lang
                $eng_join
                ORDER BY tag.name ASC";

            $query = static::query($sql, array(':lang'=>$lang));

            foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $tag) {
                $list[$tag->id] = $tag;
            }

            return $list;
        }


        public function validate (&$errors = array()) {
            if (empty($this->name))
                $errors[] = 'Falta nombre';
                //Text::get('validate-tag-name');

            if (empty($errors))
                return true;
            else
                return false;
        }

        // para añadir un nuevo tag al blog
        public function save (&$errors = array()) {
            if (!$this->validate($errors)) return false;

            $fields = array(
                'id',
                'name'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO tag SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        // para añadir un nuevo tag al post
        public function assign (&$errors = array()) {

            $fields = array(
                'tag',
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
                $sql = "REPLACE INTO post_tag SET " . $set;
                self::query($sql, $values);
                if (empty($this->id)) $this->id = self::insertId();

                return true;
            } catch(\PDOException $e) {
                $errors[] = "HA FALLADO!!! " . $e->getMessage();
                return false;
            }
        }

        /*
         * Para quitar un tag de un post
         */
        public static function remove ($tag) {

            $sql = "DELETE FROM post_tag WHERE tag = :tag";
            if (self::query($sql, array(':tag'=>$tag))) {
                return true;
            } else {
                return false;
            }

        }

    }

}
