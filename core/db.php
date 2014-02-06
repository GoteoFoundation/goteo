<?php

namespace Goteo\Core {

    class DB extends \PDO {
        public $cache = null;

        public function __construct() {

            try {

                $dsn = \GOTEO_DB_DRIVER . ':host=' . \GOTEO_DB_HOST . ';dbname=' . \GOTEO_DB_SCHEMA;

                if (defined('GOTEO_DB_PORT')) {
                    $dsn .= ';port=' . \GOTEO_DB_PORT;
                }

                //If you use the UTF-8 encoding, you have to use the fourth parameter :
                if (defined('GOTEO_DB_CHARSET') && GOTEO_DB_DRIVER == 'mysql') {
                    parent::__construct($dsn, \GOTEO_DB_USERNAME, \GOTEO_DB_PASSWORD, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8"));
                }
                else {
                    parent::__construct($dsn, \GOTEO_DB_USERNAME, \GOTEO_DB_PASSWORD);
                }

                $this->setAttribute(static::ATTR_ERRMODE, static::ERRMODE_EXCEPTION);

                if($this->cache === null && defined("SQL_CACHE_DRIVER") && SQL_CACHE_DRIVER && defined("SQL_CACHE_TIME") && SQL_CACHE_TIME) {
                    require_once PHPFASTCACHE_CLASS;

                    if(SQL_CACHE_DRIVER == "memcache") {
                        \phpFastCache::setup("storage","memcache");
                        \phpFastCache::setup("server",array(array(
                            defined("SQL_CACHE_SERVER") ? SQL_CACHE_SERVER : '127.0.0.1',
                            defined("SQL_CACHE_PORT") ? SQL_CACHE_PORT : 11211,
                            1)
                        ));
                    }
                    else {
                        \phpFastCache::setup("storage","files");
                        \phpFastCache::setup("path", GOTEO_DATA_PATH);
                    }

                    $this->cache = \phpFastCache();
                }
                if($this->cache) {
                    //no queremos que las queries vayan al servidor para preparase si usamos cache
                    $this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);

                    $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array('\Goteo\Core\CacheStatement', array($this, $this->cache)));
                }


            } catch (\PDOException $e) {
                die ('Estamos teniendo problemas tecnicos, disculpen las molestias');
            }

        }

    }

    /**
     * Despues de DB->prepare() se devuelve una instancia de este objecto
     */
    class CacheStatement extends \PDOStatement {
        public $dbh;
        public $cache = null;
        public $cache_time = \SQL_CACHE_TIME;
        public $is_select = false;
        public $cache_key = '';
        public $input_parameters = null;
        public $execute = null;
        static $queries = 0;
        static $queries_cached = 0;

        protected function __construct($dbh, $cache=null) {
            $this->dbh = $dbh;
            $this->cache = $cache;
        }

        /**
         * Si hay cache esta funcion deberia delegar la ejecución hasta que se pida un resultado
         */
        public function execute($input_parameters = null) {
            if($this->cache) {
                $query = $this->queryString;
                //Solo aplicamos el cache en sentencias SELECT
                $this->is_select = ( strtolower(rtrim(substr(ltrim($query),0 ,7))) == "select" );
                if($this->is_select) {
                    $this->cache_key        = "sql-" . md5($query . serialize($input_parameters));
                    $this->input_parameters = $input_parameters;
                    //tiempo de cache
                    //salimos, la ejecución de execute se hará cuando se pida el valor
                    return true;
                }
            }
            self::$queries++;
            //si no hay cache se comporta igual
            $this->execute = parent::execute($input_parameters);
            return $this->execute;
        }

        /**
         * Ejecucion delegada
         */
        public function _execute($params = array()) {
            try {
                if($this->execute === null) {
                    self::$queries++;
                    $this->execute =  parent::execute($params);
                }
                return $this->execute;
            } catch (\PDOException $e) {
                throw new Exception("Error PDO: " . \trace($e));
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
            if($this->cache && $this->is_select && $this->cache_time) {
                $key = $this->cache_key . "-$method-" . md5(serialize($args));
                $value = $this->cache->get($key);

                if($value !== null) {
                    self::$queries_cached++;
                    // echo "[cached [$method $class_name] cache time: [{$this->cache_time}s]";

                    //devolver el valor cacheado
                    return $value;
                }
            }
            //execute delegado si no se ha ejecutado antes
            $this->_execute($this->input_parameters);

            //obtener el valor
            $value = call_user_func_array(array($this, "parent::$method"), $args);

            if($this->cache && $this->is_select && $this->cache_time) {
                //guardar en cache
                $this->cache->set($key, $value, $this->cache_time);
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
        /**
         * Para debug, retorna un array con el numero de queries que se han obtenido de la base de datos y las cacheadas
         * @return [type] [description]
         */
        public static function getQueriesSoFar() {
            return array(self::$queries, self::$queries_cached);
        }

        /* métodos cacheables */
        public function fetchColumn() {
            return self::_cachedMethod("fetchColumn", func_get_args());
        }

        public function fetchObject() {
            return self::_cachedMethod("fetchObject", func_get_args());
        }

        public function fetchAll() {
            return self::_cachedMethod("fetchAll", func_get_args());
        }

        public function fetch() {
            return self::_cachedMethod("fetch", func_get_args());
        }

        public function columnCount() {
            return self::_cachedMethod("columnCount", func_get_args());
        }

        /* Otros metodos no cacheables susceptibles de ser usados con SELECT */
        public function rowCount() {
            return self::_nonCachedMethod("rowCount", func_get_args());
        }
        public function nextRowset() {
            return self::_nonCachedMethod("nextRowset", func_get_args());
        }
    }
}