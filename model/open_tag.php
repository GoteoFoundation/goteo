<?php

namespace Goteo\Model {

    use Goteo\Library\Check;
    
    class Open_tag extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $description,
            $post,
            $used; // numero de proyectos que usan la agrupacion

        /*
         *  Devuelve datos de una agrupacion
         */
        public static function get ($id) {

                $lang = \LANG;

                // Devolver inglés cuando la no esté traducido en idioma no-español
                if ($lang != 'es') {
                    // Si el idioma se habla en españa y no está disponible, usar 'es' y sino usar 'en' por defecto
                    $default_lang = self::default_lang($lang);

                    $qaux = Model::query(
                        "SELECT id FROM open_tag_lang WHERE id = :id AND lang = :lang",
                        array(':id' => $id, ':lang' => $lang)
                    );
                    $ok = $qaux->fetchColumn();
                    if ($ok != $id)
                        $lang = $default_lang;
                }

                $query = static::query("
                    SELECT
                        open_tag.id,
                        IFNULL(open_tag_lang.name, open_tag.name) as name,
                        IFNULL(open_tag_lang.description, open_tag.description) as description,
                        open_tag.post as post
                    FROM    open_tag
                    LEFT JOIN open_tag_lang
                        ON  open_tag_lang.id = open_tag.id
                        AND open_tag_lang.lang = :lang
                    WHERE open_tag.id = :id
                    ", array(':id' => $id, ':lang'=>$lang));
                $open_tag = $query->fetchObject(__CLASS__);

                return $open_tag;
        }

        /*
         * Lista de agrupaciones para proyectos
         * @TODO añadir el numero de usos
         */
        public static function getAll () {

            $lang = \LANG;
            
            $list = array();

            if(self::default_lang($lang)=='es')
                {          
                    // Español como alternativa

                    $sql = "SELECT
                                open_tag.id as id,
                                IFNULL(open_tag_lang.name, open_tag.name) as name,
                                IFNULL(open_tag_lang.description, open_tag.description) as description,
                                open_tag.post as post,
                                (   SELECT 
                                        COUNT(project_open_tag.project)
                                    FROM project_open_tag
                                    WHERE project_open_tag.open_tag = open_tag.id
                                ) as numProj,
                                open_tag.order as `order`
                            FROM    open_tag
                            LEFT JOIN open_tag_lang
                                ON  open_tag_lang.id = open_tag.id
                                AND open_tag_lang.lang = :lang
                            ORDER BY `order` ASC
                            ";
                }
                else
                {
                    // Inglés como alternativa

                    $sql = "SELECT
                                open_tag.id as id,
                                IFNULL(open_tag_lang.name, IFNULL(eng.name, open_tag.name)) as name,
                                IFNULL(open_tag_lang.description, IFNULL(eng.description, open_tag.description)) as description,
                                open_tag.post as post,
                                (   SELECT 
                                        COUNT(project_open_tag.project)
                                    FROM project_open_tag
                                    WHERE project_open_tag.open_tag = open_tag.id
                                ) as numProj,
                                open_tag.order as `order`
                            FROM    open_tag
                            LEFT JOIN open_tag_lang
                                ON  open_tag_lang.id = open_tag.id
                                AND open_tag_lang.lang = :lang
                            LEFT JOIN open_tag_lang as eng
                                ON  eng.id = open_tag.id
                                AND eng.lang = 'en'
                            ORDER BY `order` ASC
                            ";
                }


            $query = static::query($sql, array(':lang'=>\LANG));

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
            
            $lang = \LANG;

            $array = array ();

            try {

                 if(self::default_lang($lang)=='es')
                {          
                    // Español como alternativa

                    $sql = "SELECT 
                            open_tag.id as id,
                            IFNULL(open_tag_lang.name, open_tag.name) as name
                        FROM open_tag
                        LEFT JOIN open_tag_lang
                            ON  open_tag_lang.id = open_tag.id
                            AND open_tag_lang.lang = :lang
                        GROUP BY open_tag.id
                        ORDER BY open_tag.order ASC
                            ";
                }
                else
                {
                    // Inglés como alternativa

                    $sql = "SELECT 
                            open_tag.id as id,
                            IFNULL(open_tag_lang.name, IFNULL(eng.name, open_tag.name)) as name
                        FROM open_tag
                        LEFT JOIN open_tag_lang
                            ON  open_tag_lang.id = open_tag.id
                            AND open_tag_lang.lang = :lang
                        LEFT JOIN open_tag_lang as eng
                            ON  eng.id = open_tag.id
                            AND eng.lang = 'en'
                        GROUP BY open_tag.id
                        ORDER BY open_tag.order ASC
                            ";
                }

                $query = static::query($sql, array(':lang'=>\LANG));
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
         * Para quitar una catgoria de la tabla
         */
        public static function delete ($id) {
            
            $sql = "DELETE FROM open_tag WHERE id = :id";
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
    
}