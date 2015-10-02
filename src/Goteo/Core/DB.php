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

use Goteo\Library\Cacher;
use Goteo\Application\Config\ConfigException;

class DB extends \PDO {
    public $cache = null;
    static $cache_active = false; //para poder desactivar la cache globalmente si se necesita
    public $read_replica = null;
    public $is_select = false;
    public $type = 'master';


    /**
     * [__construct description]
     * @param Cacher|null $cache [description]
     * @param int     $debug    si debug es 1, se recojeran en el array las queries no cacheadas
     *                          si debug es 2, se recojeran todas las queries
     */
    public function __construct(Cacher $cache = null, $debug = false) {

        try {

            $dsn = \GOTEO_DB_DRIVER . ':host=' . \GOTEO_DB_HOST . ';dbname=' . \GOTEO_DB_SCHEMA;

            if (defined('GOTEO_DB_PORT') && \GOTEO_DB_PORT) {
                $dsn .= ';port=' . \GOTEO_DB_PORT;
            }

            //If you use the UTF-8 encoding, you have to use the fourth parameter :
            if (defined('GOTEO_DB_CHARSET') && GOTEO_DB_DRIVER === 'mysql') {
                parent::__construct($dsn, \GOTEO_DB_USERNAME, \GOTEO_DB_PASSWORD, array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'));
            }
            else {
                parent::__construct($dsn, \GOTEO_DB_USERNAME, \GOTEO_DB_PASSWORD);
            }

            $this->setAttribute(static::ATTR_ERRMODE, static::ERRMODE_EXCEPTION);

            if($cache instanceOf Cacher) {
                $this->cache = $cache;
            }

            //no queremos que las queries vayan al servidor para preparase si usamos cache
            $this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
            $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array('\Goteo\Core\CacheStatement', array($this, $this->cache, $debug)));

            //Preparamos un objeto para los select que lean de las replicas
            if(defined('GOTEO_DB_READ_REPLICA_HOST') && GOTEO_DB_READ_REPLICA_HOST) {

                $dsn = GOTEO_DB_DRIVER . ':host=' . GOTEO_DB_READ_REPLICA_HOST . ';dbname=' . GOTEO_DB_SCHEMA;
                if (defined('GOTEO_DB_READ_REPLICA_PORT')) {
                    $dsn .= ';port=' . \GOTEO_DB_READ_REPLICA_PORT;
                }

                $username = defined('GOTEO_DB_READ_REPLICA_USERNAME') ? GOTEO_DB_READ_REPLICA_USERNAME : GOTEO_DB_USERNAME;
                $password = defined('GOTEO_DB_READ_REPLICA_PASSWORD') ? GOTEO_DB_READ_REPLICA_PASSWORD : GOTEO_DB_PASSWORD;
                if (defined('GOTEO_DB_CHARSET') && GOTEO_DB_DRIVER == 'mysql') {
                    $this->read_replica = new \PDO($dsn, $username, $password, array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'));
                }
                else {
                    $this->read_replica = new \PDO($dsn, $username, $password);
                }
                $this->read_replica->type = 'replica';
                $this->read_replica->setAttribute(static::ATTR_ERRMODE, static::ERRMODE_EXCEPTION);

                //no queremos que las queries vayan al servidor para preparase si usamos cache
                $this->read_replica->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
                $this->read_replica->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array('\Goteo\Core\CacheStatement', array($this->read_replica, $this->cache, $debug)));

            }


        } catch (\PDOException $e) {
            throw new ConfigException("Database misconfiguration: " . $e->getMessage(), 1);
        }

    }

    /**
     * Invalidates the cache
     */
    public function cleanCache() {
        if($this->cache) $this->cache->clean();
    }

    /**
     * Override de prepare
     * @param  [type] $statement      [description]
     * @param  array  $driver_options [description]
     * @param  boolean  $select_from_replica Define si la próxima consulta se enviara a la replica (si es select)
     * @return [type]                 [description]
     */
    public function prepare($statement, $driver_options = array(), $select_from_replica = true) {

        $this->is_select = ( strtolower(rtrim(substr(ltrim($statement),0 ,7))) == 'select' );
        if($this->read_replica && $this->is_select && $select_from_replica) {
            $this->read_replica->is_select = true;
            //usamos el objecto replica
            // echo '[$statement] des de replica';
            return $this->read_replica->prepare($statement, $driver_options);
        }
        else {
            //proceso normal
            return parent::prepare($statement, $driver_options);
        }
    }

    /**
     * Para debug, retorna un array con el numero de queries que se han obtenido de la base de datos y las cacheadas
     */
    public function getQueryStats() {
        $ret = array();
        $ret['replica']['non-cached'] = \Goteo\Core\CacheStatement::$query_stats['replica'][0] . ' ( ' . \Goteo\Core\CacheStatement::$query_stats['replica'][2] . 's)';
        $ret['replica']['cached'] = \Goteo\Core\CacheStatement::$query_stats['replica'][1];
        $ret['master']['non-cached'] = \Goteo\Core\CacheStatement::$query_stats['master'][0] . ' ( ' . \Goteo\Core\CacheStatement::$query_stats['master'][2] . 's)';
        $ret['master']['cached'] = \Goteo\Core\CacheStatement::$query_stats['master'][1];
        $ret['sql_replica']['non-cached'] = \Goteo\Core\CacheStatement::$queries['replica'][0];
        $ret['sql_replica']['cached'] = \Goteo\Core\CacheStatement::$queries['replica'][1];
        $ret['sql_master']['non-cached'] = \Goteo\Core\CacheStatement::$queries['master'][0];
        $ret['sql_master']['cached'] = \Goteo\Core\CacheStatement::$queries['master'][1];
        return $ret;
    }

    /**
     * Metodo global para activar/desactivar la cache
     * Sin argumentos simplemente retorna si está o no activa
     */
    static public function cache($activate = null) {
        if($activate !== null) {
            self::$cache_active = (boolean) $activate;
        }
        return self::$cache_active;
    }
}

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
    public $input_parameters = null;
    public $execute = null;
    static $query_stats = array('replica' => array(0, 0, 0), 'master' => array(0, 0, 0)); // array(num-non-cached, num-cached, total-time-non-cached )
    static $queries = array('replica' => array(array(), array()), 'master' => array(array(), array()));
    static $queries_time = 0;
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
        if($this->cache && $this->cache_active) {
            //Solo aplicamos el cache en sentencias SELECT
            if($this->is_select) {
                $this->cache_key        = $query . serialize($input_parameters);
                $this->input_parameters = $input_parameters;
                //tiempo de cache
                //salimos, la ejecución de execute se hará cuando se pida el valor
                return true;
            }
        }
        //incrementar queries no cacheadas
        self::$query_stats[$this->dbh->type][0]++;
        //si no hay cache se comporta igual
        $t = microtime(true);
        $this->execute = parent::execute($input_parameters);
        $query_time = round(microtime(true) - $t, 4);
        self::$query_stats[$this->dbh->type][2] += $query_time;
        if($this->debug) self::$queries[$this->dbh->type][0][] = array(self::$query_stats[$this->dbh->type][0], $this->queryString, $this->input_parameters, $query_time);
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
                if($this->debug) self::$queries[$this->dbh->type][0][] = array(self::$query_stats[$this->dbh->type][0], $this->queryString, $this->input_parameters, $query_time);
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
     * Ejecución del método deseado con cache
     */
    public function _cachedMethod($method, $args=null) {
        if($this->cache && $this->is_select && $this->cache_time && $this->cache_active) {
            $key = $this->cache->getKey($this->cache_key . serialize($args), $method);
            $value = $this->cache->retrieve($key);

            if($value !== false) {
                //incrementar queries cacheadas
                self::$query_stats[$this->dbh->type][1]++;
                if($this->debug>1) self::$queries[$this->dbh->type][1][] = array(self::$query_stats[$this->dbh->type][1], $this->queryString, $this->input_parameters);

                // echo "[cached [$method $class_name] cache time: [{$this->cache_time}s]";

                //devolver el valor cacheado
                return $value;
            }
        }
        //execute delegado si no se ha ejecutado antes
        $this->_execute($this->input_parameters);

        //obtener el valor
        $value = call_user_func_array(array($this, "parent::$method"), $args);

        if($this->cache && $this->is_select && $this->cache_time && $this->cache_active) {
            //guardar en cache
            $this->cache->store($key, $value, $this->cache_time);
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
        return self::_cachedMethod('fetchColumn', func_get_args());
    }

    public function fetchObject($class_name = NULL, $ctor_args = NULL) {
        return self::_cachedMethod('fetchObject', func_get_args());
    }

    public function fetchAll($how = NULL, $class_name = NULL, $ctor_args = NULL) {
        return self::_cachedMethod('fetchAll', func_get_args());
    }

    public function fetch($how = NULL, $orientation = NULL, $offset = NULL) {
        return self::_cachedMethod('fetch', func_get_args());
    }

    public function columnCount() {
        return self::_cachedMethod('columnCount', func_get_args());
    }

    /* Otros metodos no cacheables susceptibles de ser usados con SELECT */
    public function rowCount() {
        return self::_nonCachedMethod('rowCount', func_get_args());
    }
    public function nextRowset() {
        return self::_nonCachedMethod('nextRowset', func_get_args());
    }
    /* el resto de métodos no son usados por SELECT, no hace falta definirlos otra vez*/
}

