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

use Goteo\Application\Config\ConfigException;

/**
 * Despues de DB->prepare() se devuelve una instancia de este objecto
 */
class CacheStatement extends \PDOStatement {
    public $dbh;
    public $cache = null;
    public $cache_time = 0;
    private $cache_active = true;
    public $is_select = false;
    public $cache_key = '';
    public $skip_cache = false;
    public $input_parameters = null;
    public $execute = null;
    static $query_stats = array('replica' => array(0, 0, 0), 'master' => array(0, 0, 0)); // array(num-non-cached, num-cached, total-time-non-cached )
    static $queries = array('replica' => array(array(), array()), 'master' => array(array(), array()));
    static $queries_time = 0;
    static $in_memory_cache = [];
    public $debug = false; //si debug es 1, se recojeran en el array las queries no cacheadas
                           //si debug es 2, se recojeran todas las queries

    protected function __construct($dbh, $cache=null, $debug = false) {
        $this->dbh = $dbh;
        $this->cache = $cache;
        $this->is_select = $dbh->is_select;
        $this->cache_active = \Goteo\Core\DB::$cache_active;
        if($cache) $this->cache_time = $cache->getCacheTime();
        $this->debug = $debug;
    }

    /**
     * Si hay cache esta funcion deberia delegar la ejecución hasta que se pida un resultado
     */
    public function execute($input_parameters = null) {
        $query = $this->queryString;

        // echo '['.$this->dbh->type.':'.intval($this->is_select).']';
        // Apply cache in SELECT sql
        if($this->is_select) {
            $this->cache_key        = $query . serialize($input_parameters);
            $this->input_parameters = $input_parameters;
            //tiempo de cache
            //salimos, la ejecución de execute se hará cuando se pida el valor
            return true;
        }
        //incrementar queries no cacheadas
        self::$query_stats[$this->dbh->type][0]++;
        //si no hay cache se comporta igual
        $t = microtime(true);
        $this->execute = parent::execute($input_parameters);
        $query_time = round(microtime(true) - $t, 4);
        self::$query_stats[$this->dbh->type][2] += $query_time;
        if($this->debug) self::$queries[$this->dbh->type][0][] = array(self::$query_stats[$this->dbh->type][0], $this->queryString, $input_parameters, $query_time);
        return $this->execute;
    }

    /**
     * Ejecucion delegada
     */
    public function _execute($params = array()) {
        try {
            if($this->execute === null) {
                //incrementar queries no cacheadas
                self::$query_stats[$this->dbh->type][0]++;

                $t = microtime(true);
                $this->execute =  parent::execute($params);
                $query_time = round(microtime(true) - $t, 4);
                self::$query_stats[$this->dbh->type][2] += $query_time;
                if($this->debug) self::$queries[$this->dbh->type][0][] = array(self::$query_stats[$this->dbh->type][0], $this->queryString, $params, $query_time);
            }
            return $this->execute;
        } catch (\PDOException $e) {
            // throw new ConfigException('Error PDO: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Define o consulta (sin argumentos) el tiempo de cache para la próxima query
     */
    public function cacheTime($time = null) {
        if($time !== null) {
            $this->cache_time = (int) $time;
        }
        return $this->cache_time;
    }

    /**
     * Skips any kind of cache for the next execution
     * @return [type] [description]
     */
    public function skipCache() {
        $this->skip_cache = true;
        return $this;
    }

    /**
     * Ejecución del método deseado con cache
     */
    public function _cachedMethod($method, $args=null) {
        if($this->is_select && !$this->skip_cache && $this->cache_active) {
            $value = false;
            if($this->cache && $this->cache_time) {
                $key = $this->cache->getKey($this->cache_key . serialize($args), $method);
                $value = $this->cache->retrieve($key);
            } else {
                $key = $this->cache_key . serialize($args) .'-'. $method;
                // echo "[$key]";
                // In memory cache
                if(array_key_exists($key, self::$in_memory_cache)) {
                    $value = self::$in_memory_cache[$key];
                }
            }

            if($value !== false) {
                //incrementar queries cacheadas
                self::$query_stats[$this->dbh->type][1]++;
                if($this->debug>1) self::$queries[$this->dbh->type][1][] = array(self::$query_stats[$this->dbh->type][1], $this->queryString, $this->input_parameters);

                // echo "[cached [$method $class_name] cache time: [{$this->cache_time}s]";
                $this->skip_cache = false;
                //devolver el valor cacheado
                return $value;
            }
        }
        //execute delegado si no se ha ejecutado antes
        $this->_execute($this->input_parameters);

        //obtener el valor
        $value = call_user_func_array(array($this, "parent::$method"), $args);

        if($this->is_select && !$this->skip_cache && $this->cache_active) {
            if($this->cache && $this->cache_time) {
                //guardar en cache
                $this->cache->store($key, $value, $this->cache_time);
            } else {
                // In-memory cache
                self::$in_memory_cache[$key] = $value;
            }
        }

        return $value;
    }

    /**
     * Ejecución del método deseado sin cache
     */
    public function _nonCachedMethod($method, $args=null) {
        //execute delegado si no se ha ejecutado antes
        $this->_execute($this->input_parameters);
        //obtener el valor
        return call_user_func_array(array($this, "parent::$method"), $args);
    }

    /* métodos cacheables */
    public function fetchColumn($column_number = NULL) {
        return $this->_cachedMethod('fetchColumn', func_get_args());
    }

    public function fetchObject($class_name = NULL, $ctor_args = NULL) {
        return $this->_cachedMethod('fetchObject', func_get_args());
    }

    public function fetchAll($fetch_style = NULL, $class_name = NULL, $ctor_args = NULL) {
        return $this->_cachedMethod('fetchAll', func_get_args());
    }

    public function fetch($fetch_style = NULL, $orientation = NULL, $offset = NULL) {
        return $this->_cachedMethod('fetch', func_get_args());
    }

    public function columnCount() {
        return $this->_cachedMethod('columnCount', func_get_args());
    }

    /* Otros metodos no cacheables susceptibles de ser usados con SELECT */
    public function rowCount() {
        return $this->_nonCachedMethod('rowCount', func_get_args());
    }
    public function nextRowset() {
        return $this->_nonCachedMethod('nextRowset', func_get_args());
    }
    /* el resto de métodos no son usados por SELECT, no hace falta definirlos otra vez*/
}

