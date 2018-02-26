<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Model;

use Goteo\Library\Check;
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Application\Exception\ModelException;

class Category extends \Goteo\Core\Model {

    public
        $id,
        $name,
        $description,
        $social_commitment,
        $used; // numero de proyectos que usan la categoria

    static public function getLangFields() {
        return ['name', 'description'];
    }

    /*
     *  Devuelve datos de una categoria
     */
    public static function get ($id, $lang = null) {
        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));
        $sql = "
            SELECT
                category.id,
                $fields,
                category.social_commitment
            FROM    category
            $joins
            WHERE category.id = :id
            ";
        // die(\sqldbg($sql, [':id' => $id]));
        $query = static::query($sql, array(':id' => $id));
        $category = $query->fetchObject(__CLASS__);

        return $category;
    }

    /*
     * Lista de categorias para proyectos
     * @TODO añadir el numero de usos
     */
    public static function getAll ($lang = null) {
        $list = array();

        if(!$lang) $lang = Lang::current();
        list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

        $sql="SELECT
                category.id as id,
                category.social_commitment as social_commitment,
                $fields,
                (   SELECT
                        COUNT(project_category.project)
                    FROM project_category
                    WHERE project_category.category = category.id
                ) as numProj,
                (   SELECT
                        COUNT(user_interest.user)
                    FROM user_interest
                    WHERE user_interest.interest = category.id
                ) as numUser,
                category.order as `order`
            FROM    category
            $joins
            ORDER BY `order` ASC";

        $query = static::query($sql);

        foreach ($query->fetchAll(\PDO::FETCH_CLASS, __CLASS__) as $category) {
            $list[$category->id] = $category;
        }

        return $list;
    }

    /**
     * Get all categories used in published projects
     *
     * @param void
     * @return array
     */
	public static function getNames ($lang = null) {
        if(!$lang) $lang = Lang::current();

        $array = array ();
        try {
            list($fields, $joins) = self::getLangsSQLJoins($lang, Config::get('sql_lang'));

            $sql="SELECT
                        category.id as id,
                        $fields
                    FROM category
                    $joins
                    GROUP BY category.id
                    ORDER BY category.order ASC";

            $query = static::query($sql);
            $categories = $query->fetchAll();

            foreach ($categories as $cat) {
                // la 15 es de testeos
                if ($cat[0] == 15) continue;
                $array[$cat[0]] = $cat[1];
            }

            return $array;
        } catch(\PDOException $e) {
			throw new ModelException($e->getMessage());
        }
	}

    public function validate (&$errors = array()) {
        if (empty($this->name))
            $errors[] = 'Falta nombre';
            //Text::get('mandatory-category-name');

        if (empty($errors))
            return true;
        else
            return false;
    }

    public function save (&$errors = array()) {
        if (!$this->validate($errors)) return false;

        try {
            $this->dbInsertUpdate([
                'id',
                'name',
                'description',
                'social_commitment'
            ]);
            return true;
        }
        catch(\PDOException $e) {
            $errors[] = 'Error saving category: ' . $e->getMessage();
        }
        return false;
    }

    /*
     * Para que salga antes  (disminuir el order)
     */
    public static function up ($id) {
        return Check::reorder($id, 'up', 'category', 'id', 'order');
    }

    /*
     * Para que salga despues  (aumentar el order)
     */
    public static function down ($id) {
        return Check::reorder($id, 'down', 'category', 'id', 'order');
    }

    /*
     * Orden para añadirlo al final
     */
    public static function next () {
        $query = self::query('SELECT MAX(`order`) FROM category');
        $order = $query->fetchColumn(0);
        return ++$order;

    }

    /**
     * Get a list of used keywords
     *
     * can be of users, projects or  all
     *
     */
	public static function getKeyWords ($search = null, $limit = 50) {
        $array = array ();
        try {
            $values = null;
            $sql = "SELECT DISTINCT keywords
                    FROM project
                    WHERE status > 1
                    AND keywords IS NOT NULL
                    AND keywords != ''";

            if($search) {
                $sql .= " AND keywords LIKE ?";
                $values = array("%$search%");
            }

            $sql .= ' LIMIT ' . (int)$limit;

            $query = static::query($sql, $values);
            // die(\sqldbg($sql, $values));
            $keywords = $query->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($keywords as $keyw) {
                $kw = $keyw['keywords'];
//                    $kw = str_replace('|', ',', $keyw['keywords']);
//                    $kw = str_replace(array(' ','|'), ',', $keyw['keywords']);
//                    $kw = str_replace(array('-','.'), '', $kw);
                $kwrds = preg_split('/[,;]/', $kw);

                foreach ($kwrds as $word) {
                    $tag = strtolower(trim($word));
                    if($search && stripos($word, $search) === false) continue;
                    if(!in_array($tag, $array))
                        $array[] = $tag;
                }
            }

            sort($array);

            return $array;
        } catch(\PDOException $e) {
			throw new ModelException($e->getMessage());
        }
	}

}

