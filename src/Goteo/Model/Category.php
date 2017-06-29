<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model {

    use Goteo\Library\Check;
    use Goteo\Application\Lang;
    use Goteo\Application\Config;

    class Category extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $description,
            $social_commitment,
            $used; // numero de proyectos que usan la categoria

        /*
         *  Devuelve datos de una categoria
         */
        public static function get ($id, $lang = null) {

                //Obtenemos el idioma de soporte
                $lang=self::default_lang_by_id($id, "category_lang", $lang);

                $query = static::query("
                    SELECT
                        category.id,
                        IFNULL(category_lang.name, category.name) as name,
                        IFNULL(category_lang.description, category.description) as description,
                        category.social_commitment
                    FROM    category
                    LEFT JOIN category_lang
                        ON  category_lang.id = category.id
                        AND category_lang.lang = :lang
                    WHERE category.id = :id
                    ", array(':id' => $id, ':lang'=>$lang));
                $category = $query->fetchObject(__CLASS__);

                return $category;
        }

        /*
         * Lista de categorias para proyectos
         * @TODO añadir el numero de usos
         */
        public static function getAll () {

            $list = array();

            if(Lang::current() === Config::get('lang')) {
                $different_select=" IFNULL(category_lang.name, category.name) as name,
                                    IFNULL(category_lang.description, category.description) as description";
            }
            else {
                $different_select=" IFNULL(category_lang.name, IFNULL(eng.name, category.name)) as name,
                                    IFNULL(category_lang.description, IFNULL(eng.description, category.description)) as description";
                $eng_join=" LEFT JOIN category_lang as eng
                                ON  eng.id = category.id
                                AND eng.lang = 'en'";
            }

            $sql="SELECT
                    category.id as id,
                    category.social_commitment as social_commitment,
                    $different_select,
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
                LEFT JOIN category_lang
                    ON  category_lang.id = category.id
                    AND category_lang.lang = :lang
                $eng_join
                ORDER BY `order` ASC";

            $query = static::query($sql, array(':lang'=>Lang::current()));

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
		public static function getList () {

            $array = array ();
            try {
                if(Lang::current() === Config::get('lang')) {
                    $different_select=" IFNULL(category_lang.name, category.name) as name";
                }
                else {
                    $different_select=" IFNULL(category_lang.name, IFNULL(eng.name, category.name)) as name";
                    $eng_join=" LEFT JOIN category_lang as eng
                                    ON  eng.id = category.id
                                    AND eng.lang = 'en'";
                }

                $sql="SELECT
                            category.id as id,
                            $different_select
                        FROM category
                        LEFT JOIN category_lang
                            ON  category_lang.id = category.id
                            AND category_lang.lang = :lang
                        $eng_join
                        GROUP BY category.id
                        ORDER BY category.order ASC";

                $query = static::query($sql, array(':lang'=>Lang::current()));
                $categories = $query->fetchAll();

                foreach ($categories as $cat) {
                    // la 15 es de testeos
                    if ($cat[0] == 15) continue;
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
                //Text::get('mandatory-category-name');

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
                'social_commitment'
                );

            $set = '';
            $values = array();

            foreach ($fields as $field) {
                if ($set != '') $set .= ", ";
                $set .= "`$field` = :$field ";
                $values[":$field"] = $this->$field;
            }

            try {
                $sql = "REPLACE INTO category SET " . $set;
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
		public static function getKeyWords () {
            $array = array ();
            try {

                $sql = "SELECT
                            keywords
                        FROM project
                        WHERE status > 1
                        AND keywords IS NOT NULL
                        AND keywords != ''
                        ";
/*
                     UNION
                        SELECT
                            keywords
                        FROM user
                        WHERE keywords IS NOT NULL
                        AND keywords != ''
*
 */
                $query = static::query($sql);
                $keywords = $query->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($keywords as $keyw) {
                    $kw = $keyw['keywords'];
//                    $kw = str_replace('|', ',', $keyw['keywords']);
//                    $kw = str_replace(array(' ','|'), ',', $keyw['keywords']);
//                    $kw = str_replace(array('-','.'), '', $kw);
                    $kwrds = explode(',', $kw);

                    foreach ($kwrds as $word) {
                        $array[] = strtolower(trim($word));
                    }
                }

                asort($array);

                return $array;
            } catch(\PDOException $e) {
				throw new \Goteo\Core\Exception($e->getMessage());
            }
		}

    }

}
