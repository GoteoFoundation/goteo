<?php

namespace Goteo\Model {

    use Goteo\Library\Check;
    
    class Category extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $description,
            $used; // numero de proyectos que usan la categoria

        /*
         *  Devuelve datos de una categoria
         */
        public static function get ($id) {


                $lang = \LANG;

                // Devolver inglés cuando la no esté traducido en idioma no-español
                if ($lang != 'es') {
                    // Si el idioma se habla en españa y no está disponible, usar 'es' y sino usar 'en' por defecto
                    $default_lang = self::default_lang($lang);

                    $qaux = static::query(
                        "SELECT id FROM category_lang WHERE id = :id AND lang = :lang",
                        array(':id' => $id, ':lang' => $lang)
                    );
                    $ok = $qaux->fetchColumn();
                    if ($ok != $id)
                        $lang = $default_lang;
                }        

                $query = static::query("
                    SELECT
                        category.id,
                        IFNULL(category_lang.name, category.name) as name,
                        IFNULL(category_lang.description, category.description) as description
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

            $lang=\LANG;

            $list = array();

             if(self::default_lang($lang)=='es')
                {          
                    // Español como alternativa

                    $sql ="SELECT
                    category.id as id,
                    IFNULL(category_lang.name, category.name) as name,
                    IFNULL(category_lang.description, category.description) as description,
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
                ORDER BY `order` ASC
                        ";
                }
            else
                {
                    // Inglés como alternativa

                    $sql ="SELECT
                    category.id as id,
                    IFNULL(category_lang.name, IFNULL(eng.name, category.name)) as name,
                    IFNULL(category_lang.description, IFNULL(eng.description, category.description)) as description,
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
                LEFT JOIN category_lang as eng
                    ON  eng.id = category.id
                    AND eng.lang = 'en'
                ORDER BY `order` ASC
                        ";
                }

            $query = static::query($sql, array(':lang'=>\LANG));

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

            $lang=\LANG;
            $array = array ();
            try {
                 if(self::default_lang($lang)=='es')
                {          
                    // Español como alternativa

                    $sql ="SELECT 
                            category.id as id,
                            IFNULL(category_lang.name, category.name) as name
                        FROM category
                        LEFT JOIN category_lang
                            ON  category_lang.id = category.id
                            AND category_lang.lang = :lang
                        GROUP BY category.id
                        ORDER BY category.order ASC
                        ";
                }
            else
                {
                    // Inglés como alternativa

                    $sql ="SELECT 
                            category.id as id,
                            IFNULL(category_lang.name, IFNULL(eng.name, category.name)) as name
                        FROM category
                        LEFT JOIN category_lang
                            ON  category_lang.id = category.id
                            AND category_lang.lang = :lang
                        LEFT JOIN category_lang as eng
                            ON  eng.id = category.id
                            AND eng.lang = 'en'    
                        GROUP BY category.id
                        ORDER BY category.order ASC
                        ";
                }

                $query = static::query($sql, array(':lang'=>\LANG));
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
                'description'
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
         * Para quitar una catgoria de la tabla
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM category WHERE id = :id";
            if (self::query($sql, array(':id'=>$id))) {
                return true;
            } else {
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