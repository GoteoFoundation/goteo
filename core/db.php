<?php

namespace Goteo\Core {

    class DB extends \PDO {

        public function __construct() {

            $dsn = \GOTEO_DB_DRIVER . ':host=' . \GOTEO_DB_HOST . ';dbname=' . \GOTEO_DB_SCHEMA;
            
            if (defined('GOTEO_DB_PORT')) {
                $dsn .= ';port=' . \GOTEO_DB_PORT;
            }
                       
            parent::__construct($dsn, \GOTEO_DB_USERNAME, \GOTEO_DB_PASSWORD);
            
            $this->setAttribute(static::ATTR_ERRMODE, static::ERRMODE_EXCEPTION);
        }

    }

}