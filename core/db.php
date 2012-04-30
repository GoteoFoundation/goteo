<?php

namespace Goteo\Core {

    class DB extends \PDO {

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
} catch (\PDOException $e) {
die ('Estamos teniendo problemas tecnicos, disculpen las molestias');
}


        }

    }

}