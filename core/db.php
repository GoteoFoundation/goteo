<?php
namespace Goteo\Core {

	class DB extends \PDO {
	    
    	public function __construct() {
			$file = 'config.ini';
			if (!$settings = parse_ini_file($file, TRUE)) {
				throw new exception("Unable to open {$file}.");
			}
			$dsn = "{$settings['database']['driver']}:host={$settings['database']['host']};dbname={$settings['database']['schema']}";
			if(!empty($settings['database']['port'])) {
				$dsn .= ";port={$settings['database']['port']}";
			}
			parent::__construct($dsn, $settings['database']['username'], $settings['database']['password']);
			$this->setAttribute(static::ATTR_ERRMODE, static::ERRMODE_EXCEPTION);
		}

	}

}