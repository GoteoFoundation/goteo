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
