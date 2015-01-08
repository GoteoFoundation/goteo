<?php

namespace Goteo\Model {

    use Goteo\Library\Check;

    class OpenTag extends \Goteo\Core\Model {
        //table for this model is not opentag but open_tag
        protected $Table = 'open_tag';

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

                //Obtenemos el idioma de soporte
                $lang=self::default_lang_by_id($id, 'open_tag_lang', \LANG);

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

            $list = array();

            if(self::default_lang(\LANG)=='es') {
                $different_select=" IFNULL(open_tag_lang.name, open_tag.name) as name,
                                    IFNULL(open_tag_lang.description, open_tag.description) as description";
                }
                else {
                    $different_select=" IFNULL(open_tag_lang.name, IFNULL(eng.name, open_tag.name)) as name,
                                        IFNULL(open_tag_lang.description, IFNULL(eng.description, open_tag.description)) as description";
                    $eng_join=" LEFT JOIN open_tag_lang as eng
                                    ON  eng.id = open_tag.id
                                    AND eng.lang = 'en'";
                }

                $sql="SELECT
                         open_tag.id as id,
                                $different_select,
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
                            ORDER BY `order` ASC";

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

            $array = array ();

            try {

                if(self::default_lang(\LANG)=='es') {
                $different_select=" IFNULL(open_tag_lang.name, open_tag.name) as name";
                }
                else {
                    $different_select=" IFNULL(open_tag_lang.name, IFNULL(eng.name,open_tag.name)) as name";
                    $eng_join=" LEFT JOIN open_tag_lang as eng
                                    ON  eng.id = open_tag.id
                                    AND eng.lang = 'en'";
                }

                $sql="SELECT
                            open_tag.id,
                            $different_select
                        FROM open_tag
                        LEFT JOIN open_tag_lang
                            ON  open_tag_lang.id = open_tag.id
                            AND open_tag_lang.lang = :lang
                        $eng_join
                        GROUP BY open_tag.id
                        ORDER BY open_tag.order ASC
                        ";

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

        /**
         * Static compatible version of parent delete()
         * @param  [type] $id [description]
         * @return [type]     [description]
         */
        public function delete($id = null) {
            if(empty($id)) return parent::delete();

            if(!($ob = OpenTag::get($id))) return false;
            return $ob->delete();
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
