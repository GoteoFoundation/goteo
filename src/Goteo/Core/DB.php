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

use Goteo\Application\Config;
use Goteo\Application\Config\ConfigException;
use Goteo\Library\Cacher;

class DB extends \PDO {
	public $cache = null;
    public $read_replica = null;
	static $cache_active = false;  // can be activated/deactivated globally if needed
	static $read_replica_active = false; // can be activated/deactivated globally if needed
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

			$dsn = Config::get('dsn');
			$username = Config::get('db.username', true);
			$password = Config::get('db.password', true);
			parent::__construct(
                $dsn,
                $username,
                $password,
                // Avoid stric mysql configurations (sorry we're not there yet, so many old queries)
                array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET @@SESSION.sql_mode=''")
            );

            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            // $this->setAttribute(\PDO::MYSQL_ATTR_INIT_COMMAND, "SET @@SESSION.sql_mode=''");
			if ($cache instanceOf Cacher) {
				$this->cache = $cache;
			}

			//no queremos que las queries vayan al servidor para preparase si usamos cache
			$this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
			$this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array('\Goteo\Core\CacheStatement', array($this, $this->cache, $debug)));

			//Preparamos un objeto para los select que lean de las replicas
			$dsn_replica = Config::get('dsn_replica');
			if ($dsn_replica) {
				$username = Config::get('db.replica.username') ? Config::get('db.replica.username') : $username;
				$password = Config::get('db.replica.password') ? Config::get('db.replica.password') : $password;
				$this->read_replica = new \PDO($dsn_replica, $username, $password);
				$this->read_replica->type = 'replica';
				$this->read_replica->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

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
		if ($this->cache) {
			$this->cache->clean();
		}

	}

	/**
	 * Override de prepare
	 * @param  [type] $statement      [description]
	 * @param  array  $driver_options [description]
	 * @param  boolean  $select_from_replica Define si la próxima consulta se enviara a la replica (si es select)
	 * @return [type]                 [description]
	 */
	public function prepare($statement, $driver_options = array(), $select_from_replica = true) {

		$this->is_select = (strtolower(rtrim(substr(ltrim($statement), 0, 7))) == 'select');
        if(stripos($statement, 'FROM') === false) $this->is_select = false;
        if(stripos($statement, 'LAST_INSERT_ID') !== false) $this->is_select = false;

		if ($this->read_replica && $this->is_select && $select_from_replica && static::$read_replica_active) {
			$this->read_replica->is_select = true;
			//usamos el objecto replica
			// echo '[$statement] des de replica';
			return $this->read_replica->prepare($statement, $driver_options);
		} else {
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
     * Static method that allows to activate/deactivate globally the sql internal cache
     * Withou arguments returns if it is active
     */
    static public function cache($activate = null) {
        if ($activate !== null) {
            self::$cache_active = (boolean) $activate;
        }
        return self::$cache_active;
    }

	/**
	 * Static method that allows to activate/deactivate globally if the db replica can be used (does'nt mean it will be used)
	 * Withou arguments returns if it is active
	 */
	static public function replica($activate = null) {
		if ($activate !== null) {
			self::$read_replica_active = (boolean) $activate;
		}
		return self::$read_replica_active;
	}
}
