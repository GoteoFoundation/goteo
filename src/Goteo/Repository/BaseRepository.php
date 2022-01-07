<?php

namespace Goteo\Repository;

use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Core\DB;
use Goteo\Core\Exception;
use Goteo\Library\Cacher;

abstract class BaseRepository {
    protected DB $db;
    private Cacher $cacher;
    protected ?string $Table = null;
    protected $model;

    public function __construct() {
		if ($cache_time = Config::get('db.cache.time')) {
			$this->cacher = new Cacher('sql', $cache_time);
		}

		$this->db = new DB($this->cacher, App::debug() ? 2 : false);
    }

    public function query(string $query, array $params = null, $select_from_replica = true) {

        if ($this->db === null) {
            $this->factory();
        }

        $params = func_num_args() === 2 && is_array($params) ? $params : array_slice(func_get_args(), 1);
        $result = $this->db->prepare($query, array(), $select_from_replica);
        $result->execute($params);

        return $result;
    }

    public function insertId(): int {
        try {
            //prevenimos que lea de replicas
            $query = $this->query("SELECT LAST_INSERT_ID();", null, false);
            //no queremos que lea de cache
            return $query->skipCache()->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }
}
