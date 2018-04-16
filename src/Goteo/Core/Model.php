<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Core;

use Goteo\Application\App;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Config;
use Goteo\Application\Lang;
use Goteo\Library\Cacher;

abstract class Model {

	//Override in the model the table if different from the class name
    protected $Table = null;
	static protected $Table_static = null;
	static protected $db = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		if (\func_num_args() >= 1) {
			$data = \func_get_arg(0);
			if (is_array($data) || is_object($data)) {
				$this->rebuildData((array) $data);
			}
		}

		//Default table is the name of the class
		$table = $this->getTable();
		if (empty($table)) {
			//Table by default
			$table = strtolower(get_called_class());
			if (strrpos($table, '\\') !== false) {
				$table = substr($table, strrpos($table, '\\') + 1);
			}
			$this->setTable($table);
		}
	}

    /**
     * Rebuilds model data
     * sets all $data array to vars in the model
     * if the var doesn't exist then it will be ignored.
     *
     * @param array keys if specified only this keys will processed
     *                   otherwise, all public vars will be processed
     */
	public function rebuildData(array $data, array $keys = []) {
        $public_vars = \get_public_class_vars(get_called_class());
        if(!$keys) $keys = array_keys($public_vars);
		foreach ($public_vars as $k => $v) {
			if(array_key_exists($k, $data) && in_array($k, $keys)) {
                // print_r("\n<br>$k: " . $data[$k]);
                $this->$k = $data[$k];
            }
		}
	}

	public static function factory() {
		$cacher = null;
		if ($cache_time = Config::get('db.cache.time')) {
			$cacher = new Cacher('sql', $cache_time);
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
	static public function getTableStatic() {
        if(empty(static::$Table_static)) {
            $table = strtolower(get_called_class());
            if (strrpos($table, '\\') !== false) {
                return substr($table, strrpos($table, '\\') + 1);
            }
        }
		return static::$Table_static;
	}
	/**
	 * Sets the table name
	 * @param string $table Table name
	 */
    public function setTable($table = null) {
        if ($table) {
            $this->Table = $table;
        }

        return $this;
    }

	static public function setTableStatic($table = null) {
		if ($table) {
			static::$Table_static = $table;
		}
	}

	/**
	 * Obtener.
	 * @param   type mixed  $id     Identificador
	 * @return  type object         Objeto or false if not found
	 */
	static public function get($id) {
		if (empty($id)) {
			// throw new Exception("Delete error: ID not defined!");
			return false;
		}
		$class = get_called_class();
		$ob = new $class();
		$query = static::query('SELECT * FROM ' . $ob->getTable() . ' WHERE id = :id', array(':id' => $id));
		return $query->fetchObject($class);
	}

	/**
	 * Guardar.
	 * @param   type array  $errors     Errores devueltos pasados por referencia.
	 * @return  type bool   true|false
	 */
	abstract public function save(&$errors = array());

	/**
	 * Validar.
	 * @param   type array  $errors     Errores devueltos pasados por referencia.
	 * @return  type bool   true|false
	 */
	abstract public function validate(&$errors = array());

    /**
     * Some data transformation for SQL field types
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function transformFieldValue($value) {
        if($value instanceOf \DateTime) {
            return $value->format('Y-m-d\TH:i:s');
        }
        return $value;
    }
	/**
	 * insert to sql
	 * @return [type] [description]
	 */
	public function dbInsert(array $fields) {
		$values = $set = $keys = [];
		foreach ($fields as $field) {
			if (property_exists($this, $field)) {
				$set[] = "`$field`";
				$keys[] = ":$field";
				$values[":$field"] = $this->transformFieldValue($this->$field);
			}
		}
		if (empty($values)) {
			throw new \PDOException("No fields specified!", 1);
		}

		$sql = 'INSERT INTO `' . $this->Table . '` (' . implode(',', $set) . ') VALUES (' . implode(',', $keys) . ')';
		// echo \sqldbg($sql, $values);
		$res = self::query($sql, $values);
		return $res;
	}

	/**
	 * update to sql
	 * @return [type] [description]
	 */
	public function dbUpdate(array $fields, array $where = ['id']) {
		$values = $set = [];
		foreach ($fields as $field) {
			if (property_exists($this, $field)) {
				$set[] = "`$field` = :$field";
				$values[":$field"] = $this->transformFieldValue($this->$field);
			}
		}
		$clause = [];
		foreach ($where as $field) {
			if (property_exists($this, $field)) {
				$clause[] = "`$field` = :$field";
				$values[":$field"] = $this->transformFieldValue($this->$field);
			} else {
				throw new \PDOException("Property $field does not exists!", 1);

			}
		}
		if (empty($values)) {
			throw new \PDOException("No fields specified!", 1);
		}

		$sql = 'UPDATE `' . $this->Table . '` SET ' . implode(',', $set) . ' WHERE ' . implode(' AND ', $clause);
		// die(\sqldbg($sql, $values));
		return self::query($sql, $values);
	}

	/**
	 * Authomatic insert or update behaviour
	 * Expects a id property
	 * @return [type] [description]
	 */
	public function dbInsertUpdate(array $fields, array $where = ['id']) {
		if ($this->id) {
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
	public function dbDelete(array $where = ['id']) {
		$clause = [];
		foreach ($where as $field) {
			if (property_exists($this, $field)) {
				$clause[] = "`$field` = :$field ";
				$values[":$field"] = $this->$field;
			} else {
				throw new \PDOException("Property $field does not exists!", 1);
			}
		}
		if (empty($values)) {
			throw new \PDOException("No fields specified!", 1);
		}

		$sql = 'DELETE FROM `' . $this->Table . '` WHERE ' . implode(' AND ', $clause);
        // echo \sqldbg($sql, $values);
		self::query($sql, $values);
		return true;
	}

	/**
	 * Statically delete without exception.
	 * TODO: remove this method...
	 * @return  type bool   true|false
	 */
	public static function delete($id, &$errors = array()) {
		if (empty($id)) {
			// throw new Exception("Delete error: ID not defined!");
			return false;
		}
		$class = get_called_class();
		$ob = new $class(['id' => $id]); //

		try {
			if ($ob->dbDelete()) {
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
	public static function query($query, $params = null, $select_from_replica = true) {

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
		if ($filters) {
			$add = array();
			$i = 0;
			foreach ($filters as $key => $val) {
				$values[":item$i"] = $val;
				$add[] = "`$key` $comparator :item$i";
				$i++;
			}
			if ($add) {
				$sql .= ' WHERE ' . implode(" $joiner ", $add);
			}

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
			return $query->skipCache()->fetchColumn();
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
	public static function idealiza($value, $punto = false, $enye = false) {
		$id = trim($value);
		// Acentos
		$table = array(
			'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
			'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
			'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
			'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
			'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
			'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
			'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ý' => 'y',
			'þ' => 'b', 'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r', 'ª' => 'a', 'º' => 'o', 'ẃ' => 'w', 'Ẃ' => 'Ẃ', 'ẁ' => 'w', 'Ẁ' => 'Ẃ', '€' => 'eur',
			'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ś' => 's', 'Ś' => 'S', 'ẅ' => 'w', 'Ẅ' => 'W',
			'!' => '', '¡' => '', '?' => '', '¿' => '', '@' => '', '^' => '', '|' => '', '#' => '', '~' => '',
			'%' => '', '$' => '', '*' => '', '+' => '', '.' => '-', '`' => '', '´' => '', '’' => '', '”' => '-', '“' => '-',
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
		if (!$enye) {
            $id = preg_replace('/[^\x20-\x7e]*/', '', $id);
        }
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
		if ($lang == Config::get('sql_lang')) {
			return $lang;
		}

		return Lang::getDefault($lang);
	}

	/**
	 * Comprueba para una entrada individual el idioma por defecto(de soporte) para un idioma determinado.
	 *
	 * @param string $id, string $table, string $lang
	 * @return  string $lang
	 * TODO: hacer esto de otra manera
	 */
	public static function default_lang_by_id($id, $table, $lang = null) {
		// Devolver inglés cuando no esté traducido en idioma no-español
		if ($lang != Config::get('sql_lang')) {
			if (empty($lang)) {
				$lang = Lang::current();
			}

			// Si el idioma se habla en españa y no está disponible, usar 'es' y sino usar 'en' por defecto
			$fallback = self::default_lang($lang);
			$sql = "SELECT id FROM `{$table}` WHERE id = :id AND lang = :lang";
			$values = array(':id' => $id, ':lang' => $lang);
			$qaux = static::query($sql, $values);
			// echo \sqldbg($sql, $values);
			$ok = $qaux->fetchColumn();
			// print("$ok = $id $lang");
			if ($ok != $id) {
				$lang = $fallback;
			}

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
        }
		return false;
	}


    /**
     * Removes a lang entry
     * @param  [type] $id    [description]
     * @param  [type] $table [description]
     * @return [type]        [description]
     */
    public function removeLang($lang) {
        try {
            static::query("DELETE FROM `{$this->Table}_lang` WHERE id = :id AND lang = :lang", array(':id' => $this->id, ':lang' => $lang));
            return true;
        } catch (\Exception $e) {
        }
        return false;
    }

    /**
     * Returns all translations available
     */
    public function getLangsAvailable() {
        $langs = [];
        try {
            if($query = static::query("SELECT GROUP_CONCAT(lang) AS langs FROM `{$this->Table}_lang` WHERE id = :id GROUP BY id", array(':id' => $this->id))) {
                $res = $query->fetchColumn();
                if($res) $langs = explode(',', $res);
            }
        } catch (\Exception $e) {
        }
        return $langs;
    }

    /**
     * Should return fore each model witch fields are translated
     * @return array ['field1', 'field2', ...]
     */
    static public function getLangFields() {
        return [];
    }

    /**
     * Returns the fields and Join parts to use in a SQL query
     * Check Model\Project::get, Model\Project\Reward::getAll for examples on how to use this
     *
     * @param  string $lang to obtain the data for (will return a fallback language if not exists)
     * @param  string $lang_model  if empty and $model_join_id is also empty the fallback lang will be used from the main model
     *                             (this means the model MUST have the `lang` field)
     *                             if defined and $model_join_id is empty, will be used as the fallback language
     *                             (usually, just pass Config::get('sql_lang') for fallback)
     *                             if defined and $model_join_id is also defined,
     *                                will be used as the name of the table to JOIN to get the fallback language
     * @param  string $model_join_id the field in the table_lang to use with the JOIN table.id
     * @param string $model must be a valid Goteo\Model\SomeModel defaults to calling model
     * @return array  [fields, joins]
     */
    static public function getLangsSQLJoins($lang, $lang_model=null, $model_join_id=null, $model = null) {
        if($model) $fields = $model::getLangFields();
        else       $fields = static::getLangFields();
        if(!$fields) throw new ModelException('This method requires self::getLangFields() to return the fields to translate');

        if($model) $table = $model::getTableStatic();
        else       $table = static::getTableStatic();

        if(!$lang) {
            return ["`$table`.`".implode("`,\n`$table`.`", $fields).'`', ''];
        }
        $fallback_lang = Lang::getFallback($lang);
        $default_lang = ($lang_model && !$model_join_id) ? $lang_model : Config::get('sql_lang');
        $sql_fields = [];
        // echo "\nSQL_LANG[" . Config::get('sql_lang') ."] LANG_MODE: [$lang_model] FALLBACK: [$fallback_lang]\n";
        $sql_joins = [];
        foreach($fields as $field) {
            if(!$lang_model && !$model_join_id) {
                $sql_fields[] = "IF(`$table`.lang='$lang', `$table`.`$field`, IFNULL(IFNULL(b.`$field`,c.`$field`), `$table`.`$field`)) AS `$field`";
            } elseif($lang_model && $model_join_id) {
                $sql_fields[] = "IF(m.lang='$lang', `$table`.`$field`, IFNULL(IFNULL(b.`$field`,c.`$field`), `$table`.`$field`)) AS `$field`";
            } else {
                $sql_fields[] = "IF('$default_lang'='$lang', `$table`.`$field`, IFNULL(IFNULL(b.`$field`,c.`$field`), `$table`.`$field`)) AS `$field`";
            }
        }
        if(!$lang_model && !$model_join_id) {
            $sql_joins[] = "LEFT JOIN `{$table}_lang` b ON `$table`.id=b.id AND b.lang='$lang' AND b.lang!=`$table`.lang";
            $sql_joins[] = "LEFT JOIN `{$table}_lang` c ON `$table`.id=c.id AND c.lang='$fallback_lang' AND c.lang!=`$table`.lang";
        } elseif($lang_model && $model_join_id) {
            $sql_joins[] = "RIGHT JOIN `{$lang_model}` m ON m.id=`$table`.`$model_join_id`";
            $sql_joins[] = "LEFT JOIN `{$table}_lang` b ON `$table`.id=b.id AND b.lang='$lang' AND b.lang!=m.lang";
            $sql_joins[] = "LEFT JOIN `{$table}_lang` c ON `$table`.id=c.id AND c.lang='$fallback_lang' AND c.lang!=m.lang";
        } else {
            $sql_joins[] = "LEFT JOIN `{$table}_lang` b ON `$table`.id=b.id AND b.lang='$lang' AND b.lang!='$default_lang'";
            $sql_joins[] = "LEFT JOIN `{$table}_lang` c ON `$table`.id=c.id AND c.lang='$fallback_lang' AND c.lang!='$default_lang'";
        }
        return [implode(",\n", $sql_fields), implode("\n", $sql_joins)];
    }

    /**
     * Returns percent (from 0 to 100) translations
     */
    public function getLangsPercent($lang) {
        $fields = static::getLangFields();
        if(!$fields) throw new ModelException('This method requires self::getLangFields() to return the fields to translate');

        try {
            $conditions = array_map(function($el){
                return "IF(m.$el IS NULL OR m.$el = '', 0, 1) AS $el,
                        IF(l.$el IS NULL OR l.$el = '', 0, 1) AS {$el}_lang";
            }, $fields);
            $sql = "SELECT " . implode(',', $conditions) . "
                    FROM `{$this->Table}_lang` l
                    INNER JOIN `{$this->Table}` m ON m.id = l.id
                    WHERE l.lang = :lang AND m.id = :id";
            $values = [':lang' => $lang, ':id' => $this->id];
            // die(\sqldbg($sql, $values));
            if($query = static::query($sql, $values)) {
                $translated = 0;
                $total = 0;
                $ob = $query->fetchObject();
                foreach($fields as $field) {
                    if($ob->{$field}) {
                        $translated += $ob->{$field.'_lang'};
                        $total ++;
                    }
                }
                if($total) return 100 * $translated / $total;
            }
        } catch (\Exception $e) {}
        return 0;
    }

    /**
     * Returns percent (from 0 to 100) translations
     * by grouping all items sharing some common keys
     */
    public function getLangsGroupPercent($lang, array $keys) {
        $fields = static::getLangFields();
        if(!$fields) throw new ModelException('This method requires self::getLangFields() to return the fields to translate');

        try {
            $conditions = array_map(function($el){
                return "IF(m.$el IS NULL OR m.$el = '', 0, 1) AS $el,
                        IF(l.$el IS NULL OR l.$el = '', 0, 1) AS {$el}_lang";
            }, $fields);
            $sql = "SELECT " . implode(',', $conditions) . "
                    FROM `{$this->Table}_lang` l
                    INNER JOIN `{$this->Table}` m ON m.id = l.id
                    WHERE l.lang = :lang";
            $values = [':lang' => $lang];
            foreach($keys as $key) {
                $sql .= " AND m.$key = :$key";
                $values[":$key"] = $this->{$key};
            }
            // die(\sqldbg($sql, $values));
            if($query = static::query($sql, $values)) {
                $translated = 0;
                $total = 0;
                foreach($query->fetchAll(\PDO::FETCH_OBJ) as $ob) {
                    foreach($fields as $field) {
                        if($ob->{$field}) {
                            $translated += $ob->{$field.'_lang'};
                            $total ++;
                        }
                    }
                }
                if($total) return 100 * $translated / $total;
            }

        } catch (\Exception $e) {}
        return 0;
    }

    /**
     * Returns lang object
     */
    public function getLang($lang) {
        try {
            $sql = "SELECT * FROM `{$this->Table}_lang` WHERE id = :id AND lang = :lang";
            $values = array(':id' => $this->id, ':lang' => $lang);
            // die(\sqldbg($sql, $values));
            if($query = static::query($sql, $values)) {
                return $query->fetchObject();
            }
        } catch (\Exception $e) {}
        return null;
    }

    /**
     * Returns lang object
     */
    public function getAllLangs() {
        try {
            $sql = "SELECT * FROM `{$this->Table}_lang` WHERE id = :id";
            $values = array(':id' => $this->id);
            // die(\sqldbg($sql, $values));
            if($query = static::query($sql, $values)) {
                return $query->fetchAll(\PDO::FETCH_OBJ);
            }
        } catch (\Exception $e) {}
        return [];
    }

    /**
     * Save lang info in a generic way
     */
    public function setLang($lang, $data = [], array &$errors = []) {

        $fields = static::getLangFields();
        if(!$fields) throw new ModelException('This method requires self::getLangFields() to return the fields to translate');

        $update = ["`id` = :id", "`lang` = :lang"];
        $insert = ["`id`" => ":id", "`lang`" => ":lang"];
        $values[':id'] = $this->id;
        $values[':lang'] = $lang;
        foreach ($data as $key => $val) {
            if(in_array($key, $fields) || property_exists($this, $key)) {
                $values[":$key"] = $val;
                $update[] = "`$key` = :$key";
                $insert["`$key`"] = ":$key";
            }
        }

        try {
            $sql = "INSERT INTO `{$this->Table}_lang`
                (" . implode(', ', array_keys($insert)) . ")
                VALUES (" . implode(', ', $insert) . ")
                ON DUPLICATE KEY UPDATE " . implode(', ', $update);
            // die(\sqldbg($sql, $values));
            self::query($sql, $values);

            return true;
        } catch(\PDOException $e) {
            // die($e->getMessage());
            $errors[] = "Error saving language data for {$this->Table}. " . $e->getMessage();
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
