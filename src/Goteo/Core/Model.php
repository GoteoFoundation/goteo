<?php

namespace Goteo\Core {

    use Goteo\Application\App;
	use Goteo\Core\Error,
        Goteo\Library\Cacher;

    abstract class Model {

        //Override in the model the table if different from the class name
        protected $Table = null;
        static $db = null;

        /**
         * Constructor.
         */
        public function __construct () {
            if (\func_num_args() >= 1) {
                $data = \func_get_arg(0);
                if (is_array($data) || is_object($data)) {
                    foreach ($data as $k => $v) {
                        $this->$k = $v;
                    }
                }
            }

            //Default table is the name of the class
            $table = $this->getTable();
            if(empty($table)) {
                //Table by default
                $table = strtolower(get_called_class());
                if(strrpos($table, '\\') !== false) {
                    $table = substr($table, strrpos($table, '\\') + 1);
                }
                $this->setTable($table);
            }
        }

        public static function factory() {
            $cacher = null;
            if(defined('SQL_CACHE_TIME') && SQL_CACHE_TIME) {
                $cacher = new Cacher('sql', SQL_CACHE_TIME);
            }

            self::$db = new DB($cacher, App::debug() ? 2 : false);
        }

        /**
         * Get the table name
         * @return string Table name
         */
        public function getTable() {
            return $this->Table;
        }
        /**
         * Sets the table name
         * @param string $table Table name
         */
        public function setTable($table = null) {
            if($table) $this->Table = $table;
            return $this;
        }

        /**
         * Obtener.
         * @param   type mixed  $id     Identificador
         * @return  type object         Objeto or false if not found
         */
        static public function get ($id) {
            if(empty($id)) {
                // throw new Exception("Delete error: ID not defined!");
                return false;
            }
            $class = get_called_class();
            $ob = new $class();
            static::query('SELECT * FROM ' . $ob->getTable() . ' WHERE id = :id', array(':id' => $id));
            return $query->fetchObject(__CLASS__);
        }

        /**
		 * Guardar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
         abstract public function save (&$errors = array());

        /**
         * Validar.
         * @param   type array  $errors     Errores devueltos pasados por referencia.
         * @return  type bool   true|false
         */
        abstract public function validate (&$errors = array());

        /**
         * insert to sql
         * @return [type] [description]
         */
        public function dbInsert(array $fields) {
            $values = $set = $keys = [];
            foreach($fields as $field) {
                if(property_exists($this, $field)) {
                    $set[] = "`$field`";
                    $keys[] = ":$field";
                    $values[":$field"] = $this->$field;
                }
            }
            if(empty($values)) throw new \PDOException("No fields specified!", 1);
            $sql = 'INSERT INTO `' . $this->Table . '` (' . implode(',', $set) . ') VALUES (' . implode(',', $keys) . ')';
            // print_r($values);die($sql);
            $res = self::query($sql, $values);
            return $res;
        }


        /**
         * update to sql
         * @return [type] [description]
         */
        public function dbUpdate(array $fields, array $where = ['id']) {
            $values = $set = [];
            foreach($fields as $field) {
                if(property_exists($this, $field)) {
                    $set[] = "`$field` = :$field ";
                    $values[":$field"] = $this->$field;
                }
            }
            $clause = [];
            foreach($where as $field) {
                if(property_exists($this, $field)) {
                    $clause[] = "`$field` = :$field ";
                    $values[":$field"] = $this->$field;
                }
                else {
                    throw new \PDOException("Property $field does not exists!", 1);

                }
            }
            if(empty($values)) throw new \PDOException("No fields specified!", 1);
            $sql = 'UPDATE `' . $this->Table . '` SET ' . implode(',', $set) . ' WHERE ' . implode(' AND ', $clause);

            return self::query($sql, $values);
        }


        /**
         * Authomatic insert or update behaviour
         * Expects a id property
         * @return [type] [description]
         */
        public function dbInsertUpdate(array $fields, array $where = ['id']) {
            if($this->id) {
                $ok = $this->dbUpdate($fields, $where);
            } else {
                $ok = $this->dbInsert($fields);
                $this->id = static::insertId();
            }
            return $ok;
        }


        /**
         * Delete
         * @return  type bool   true|false
         */
        public function dbDelete (array $where = ['id']) {
            $clause = [];
            foreach($where as $field) {
                if(property_exists($this, $field)) {
                    $clause[] = "`$field` = :$field ";
                    $values[":$field"] = $this->$field;
                }
                else {
                    throw new \PDOException("Property $field does not exists!", 1);
                }
            }
            if(empty($values)) throw new \PDOException("No fields specified!", 1);
            $sql = 'DELETE FROM `' . $this->Table . '` WHERE ' . implode(' AND ', $clause);

            self::query($sql, $values);
            return true;
        }

        /**
         * Statically delete without exception.
         * TODO: remove this method...
         * @return  type bool   true|false
         */
        public static function delete ($id, &$errors = array()) {
            if(empty($id)) {
                // throw new Exception("Delete error: ID not defined!");
                return false;
            }
            $class = get_called_class();
            $ob = new $class(['id' => $id]); //

            try {
                if($ob->dbDelete()) {
                    return true;
                }
                $errors[] = 'Unknow error. Empty id?';
            } catch (\PDOException $e) {
                $errors[] = 'Delete error. ' . $e->getMessage();
                return false;
            }

            return false;
        }

        /**
         * Consulta.
         * Devuelve un objeto de la clase PDOStatement
         * http://www.php.net/manual/es/class.pdostatement.php
         *
         * @param   type string $query      Consulta SQL
         * @param   type array  $params     Parámetros
         * $return  type object PDOStatement
         */
        public static function query ($query, $params = null, $select_from_replica = true) {

            if (self::$db === null) {
                self::factory();
            }

            $params = func_num_args() === 2 && is_array($params) ? $params : array_slice(func_get_args(), 1);

            //si no queremos leer de la replica se lo decimos
            $result = self::$db->prepare($query, array(), $select_from_replica);

            $result->execute($params);

            return $result;

        }

        /**
         * Retorna el total de elementos de la tabla
         * @param $filters array of pair/values to filter the table
         */
        public static function dbCount($filters = array(), $comparator = '=', $joiner = 'AND') {
            $clas = get_called_class();
            $instance = new $clas;
            $sql = 'SELECT COUNT(*) FROM ' . $instance->getTable();
            $values = array();
            if($filters) {
                $add = array();
                $i=0;
                foreach($filters as $key => $val) {
                    $values[":item$i"] = $val;
                    $add[] = "`$key` $comparator :item$i";
                    $i++;
                }
                if($add) $sql .= ' WHERE ' . implode(" $joiner ", $add);
            }
            try {
                return (int) self::query($sql, $values)->fetchColumn();
            } catch (\PDOException $e) {
            }
            return 0;
        }

        /**
         * Clears the sql cache
         * @return [type] [description]
         */
        static function cleanCache() {
            return (new Cacher('sql'))->clean();
        }

        /**
         * Devuelve el id autoincremental generado en la utima consulta, si se
         * ha generado uno.
         *
         * @return  int Id de `AUTO_INCREMENT` o `0`, si la ultima consulta no
         *          ha generado ninguna valor autoincremental.
         */
        public static function insertId() {

            try {
                //prevenimos que lea de replicas
                $query = static::query("SELECT LAST_INSERT_ID();", null, false);
                //no queremos que lea de cache
                $query->cacheTime(0);
                return $query->fetchColumn();
            } catch (\Exception $e) {
                return 0;
            }
        }

        /**
         * Formato.
         * Formatea una cadena para ser usada como id varchar(50)
         *
         * @param string $value
         * @return string $id
         */
        public static function idealiza ($value, $punto = false, $enye = false) {
            $id = trim($value);
            // Acentos
            $table = array(
                'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
                'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
                'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
                'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
                'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
                'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y',
                'þ'=>'b', 'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', 'ª'=>'a', 'º'=>'o', 'ẃ'=>'w', 'Ẃ'=>'Ẃ', 'ẁ'=>'w', 'Ẁ'=>'Ẃ', '€'=>'eur',
                'ý'=>'y', 'Ý'=>'Y', 'ỳ'=>'y', 'Ỳ'=>'Y', 'ś'=>'s', 'Ś'=>'S', 'ẅ'=>'w', 'Ẅ'=>'W',
                '!'=>'', '¡'=>'', '?'=>'', '¿'=>'', '@'=>'', '^'=>'', '|'=>'', '#'=>'', '~'=>'',
                '%'=>'', '$'=>'', '*'=>'', '+'=>'', '.'=>'-', '`'=>'', '´'=>'', '’'=>'', '”'=>'-', '“'=>'-'
            );

            if ($punto) {
                unset($table['.']);
            }

            if ($enye) {
                unset($table['Ñ']);
                unset($table['ñ']);
            }

            $id = strtr($id, $table);
            $id = strtolower($id);

            // Separadores
            $id = preg_replace("/[\s\,\(\)\[\]\:\;\_\/\"\'\{\}]+/", "-", $id);
            $id = substr($id, 0, 50);

            $id = trim($id, '-');

            return $id;
        }

          /**
         * Devuelve el idioma por defecto(de soporte) para un idioma determinado, a la hora de obtener algún tipo de texto.
         *
         * @param string $lang
         * @return  string $default_lang
         */
        public static function default_lang($lang) {
            return \Goteo\Application\Lang::getDefault($lang);
        }

          /**
         * Comprueba para una entrada individual el idioma por defecto(de soporte) para un idioma determinado.
         *
         * @param string $id, string $table, string $lang
         * @return  string $lang
         * TODO: hacer esto de otra manera
         */
         public static function default_lang_by_id($id, $table, $lang) {

            // Devolver inglés cuando no esté traducido en idioma no-español
            if ($lang != 'es') {
                // Si el idioma se habla en españa y no está disponible, usar 'es' y sino usar 'en' por defecto
                $default_lang = self::default_lang($lang);

                $qaux = static::query(
                    "SELECT id FROM `{$table}` WHERE id = :id AND lang = :lang",
                    array(':id' => $id, ':lang' => $lang)
                );
                $ok = $qaux->fetchColumn();
                if ($ok != $id)
                    $lang = $default_lang;
            }

            return $lang;
        }

        /*
         *   Metodo para marcar como pendiente todas las traducciones de cierto registro
         */
        public static function setPending($id, $table) {

            try {
                static::query("UPDATE `{$table}_lang` SET `pending` = 1 WHERE id = :id", array(':id' => $id));
                return true;
            } catch (\Exception $e) {
                return false;
            }

        }

        /**
         * Cuenta el numero de items y lo divide en páginas
         * @param type $sql
         * @param int $page Numero de página que se muestra
         * @param int $items_per_page
         * @return array ($pages,$offset)
         * TODO: elimninar este metodo
         */
        public static function doPagination($sql, $values, &$page, $items_per_page = 9) {

            $query = self::query($sql, $values);
            $query->cacheTime(defined('SQL_CACHE_LONG_TIME') ? SQL_CACHE_LONG_TIME : 3600);

            $total = $query->fetchColumn();

            if ($total == 0) {
                $page = 1;
            } elseif ($page > $total) {
                $page = $total;
            } elseif ($page < 1) {
                $page = 1;
            }

            $offset = $items_per_page * ($page - 1);
            $pages = ceil($total / $items_per_page);

            return array('pages' => $pages, 'offset' => $offset);
        }

    }

}
