<?php

namespace Goteo\Model;

use Goteo\Library\Check;
use Goteo\Application\Lang;
use Goteo\Application\Config;

class OpenTag extends \Goteo\Core\Model {
    //table for this model is not opentag but open_tag
    protected $Table = 'open_tag';
    protected static $Table_static = 'open_tag';

    public
        $id,
        $name,
        $description,
        $post,
        $used; // numero de proyectos que usan la agrupacion

    static public function getLangFields() {
        return ['name', 'description'];
    }

    /*
     *  Devuelve datos de una agrupacion
     */
    public static function get ($id, $lang = null) {
        //Obtenemos el idioma de soporte
        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $query = static::query("
            SELECT
                open_tag.id,
                $fields,
                open_tag.post as post
            FROM    open_tag
            $joins
            WHERE open_tag.id = :id
            ", array(':id' => $id));

        return $query->fetchObject(__CLASS__);
    }

    /*
     * Lista de agrupaciones para proyectos
     * @TODO añadir el numero de usos
     */
    public static function getAll ($lang = null) {
        $list = array();

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql="SELECT
                open_tag.id as id,
                $fields,
                open_tag.post as post,
                (   SELECT
                        COUNT(project_open_tag.project)
                    FROM project_open_tag
                    WHERE project_open_tag.open_tag = open_tag.id
                ) as numProj,
                open_tag.order as `order`
            FROM    open_tag
            $joins
            ORDER BY `order` ASC";

        $query = static::query($sql);

        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $open_tag) {
            $list[$open_tag->id] = $open_tag;
        }

        return $list;
    }

    /**
     * Get all open_tags used in published projects
     *
     * @param void
     * @return array
     */
	public static function getList () {
        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $array = array ();

        try {

            $sql="SELECT
                        open_tag.id,
                        $fields
                    FROM open_tag
                    LEFT JOIN open_tag_lang
                        ON  open_tag_lang.id = open_tag.id
                        AND open_tag_lang.lang = :lang
                    $joins
                    GROUP BY open_tag.id
                    ORDER BY open_tag.order ASC
                    ";

            $query = static::query($sql);
            $open_tags = $query->fetchAll();
            foreach ($open_tags as $cat) {
                $array[$cat[0]] = $cat[1];
            }

            return $array;
        } catch(\PDOException $e) {
			throw new \Goteo\Core\Exception($e->getMessage());
        }
	}


    public function validate (&$errors = array()) {
        if (empty($this->name))
            $errors[] = 'Falta nombre';
            //Text::get('mandatory-open_tag-name');

        if (empty($errors))
            return true;
        else
            return false;
    }

    public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

        $fields = array(
            'id',
            'name',
            'description',
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
            $sql = "REPLACE INTO open_tag SET " . $set;
            self::query($sql, $values);
            if (empty($this->id)) $this->id = self::insertId();

            return true;
        } catch(\PDOException $e) {
            $errors[] = "HA FALLADO!!! " . $e->getMessage();
            return false;
        }
    }

    /*
     * Para que salga antes  (disminuir el order)
     */
    public static function up ($id) {
        return Check::reorder($id, 'up', 'open_tag', 'id', 'order');
    }

    /*
     * Para que salga despues  (aumentar el order)
     */
    public static function down ($id) {
        return Check::reorder($id, 'down', 'open_tag', 'id', 'order');
    }

    /*
     * Orden para añadirlo al final
     */
    public static function next () {
        $query = self::query('SELECT MAX(`order`) FROM open_tag');
        $order = $query->fetchColumn(0);
        return ++$order;

    }

}
