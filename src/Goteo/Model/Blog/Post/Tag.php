<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Model\Blog\Post;

use Goteo\Application\Lang;
use Goteo\Application\Config;

class Tag extends \Goteo\Core\Model {

    public
        $id,
        $name,
        $post,
        $tag;


    public static function getLangFields() {
        return ['name'];
    }

    /*
     *  Devuelve datos de una comentario
     */
    public static function get ($id, $lang = null) {
        if(!$lang) $lang = Lang::current();
        //Obtenemos el idioma de soporte
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('lang'));

        $query = static::query("
            SELECT
                tag.id as id,
                $fields
            FROM    tag
            $joins
            WHERE tag.id = :id
            ", array(':id' => $id));

        return $query->fetchObject(__CLASS__);
    }

    /*
     * Lista de tags
     * de un post si recibe el parametro
     */
    public static function getAll ($post = null) {
        $list = array();

        $values = [];
        list($fields, $joins) = self::getLangsSQLJoins(Lang::current(), Config::get('lang'));

        $sql = "
            SELECT
                tag.id as id,
                $fields
            FROM    tag
            $joins
            ";

        if (!empty($post)) {
            $sql .= "INNER JOIN post_tag
                ON tag.id = post_tag.tag
                AND post_tag.post = :post
                ";
            $values[':post'] = $post;
        }

        $sql .= "ORDER BY tag.name ASC";

        // die(\sqldbg($sql, $values));
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

        list($fields, $joins) = self::getLangsSQLJoins(Lang::current(), Config::get('lang'));

        $sql = "
            SELECT
                tag.id as id,
                $fields,
                (   SELECT
                    COUNT(post_tag.post)
                    FROM post_tag
                    WHERE post_tag.tag = tag.id
                ) as used
            FROM    tag
            $joins
            ORDER BY tag.name ASC";

        $query = static::query($sql);

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
            $errors[] = $e->getMessage();
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
            $errors[] = $e->getMessage();
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

