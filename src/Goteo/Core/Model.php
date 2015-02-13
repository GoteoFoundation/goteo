<?php

namespace Goteo\Core {

	use Goteo\Core\Error,
        Goteo\Library\Cacher;

    abstract class Model {

        //Override in the model the table if different from the class name
        protected $Table = null;

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
        abstract static public function get ($id);

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
         * Borrar.
         * @return  type bool   true|false
         */
        public function delete () {
            $id = $this->id;
            if(empty($id)) {
                // throw new Exception("Delete error: ID not defined!");
                return false;
            }

            $sql = 'DELETE FROM ' . $this->Table . ' WHERE id = ?';
            // var_dump($this);
            // echo get_called_class()." $sql $id\n";
            try {
                self::query($sql, array($id));
            } catch (\PDOException $e) {
                // throw new Exception("Delete error in $sql");
                return false;
            }
            return true;
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

            static $db = null;

            if ($db === null) {
                $cacher = null;
                if(defined('SQL_CACHE_TIME') && SQL_CACHE_TIME) {
                    $cacher = new Cacher('sql', SQL_CACHE_TIME);
                }

                $db = new DB($cacher);
            }

            $params = func_num_args() === 2 && is_array($params) ? $params : array_slice(func_get_args(), 1);

            //si no queremos leer de la replica se lo decimos
            $result = $db->prepare($query, array(), $select_from_replica);

            try {

                $result->execute($params);

                return $result;

            } catch (\PDOException $e) {
                throw new Exception("Error PDO: " . \trace($e));
            }

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

            if(!is_null($lang))
            {
                // Si el idioma se habla en España y no está disponible, usar 'es' y sino usar 'en' por defecto
                // Julian: 22/01/2015 : el texto de referencia para italiano es también español
                $default_lang = (in_array($lang, array('es','ca', 'gl', 'eu', 'en', 'it'))) ? 'es' : 'en';

            return $default_lang;
            } else {

                // @ FIXME
                // si $lang es null (piden el idioma original), devolvemos 'es' a piñon
                // porque en los modelos se hace ``self::default_lang($lang)=='es'``
                // y si no damos 'es' mete el IFNULL para inglés y (al no tener registro para "*_lang.lang = null") devuelve contenido en inglés
                return 'es';
            }

        }

          /**
         * Comprueba para una entrada individual el idioma por defecto(de soporte) para un idioma determinado.
         *
         * @param string $id, string $table, string $lang
         * @return  string $lang
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
