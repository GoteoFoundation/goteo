<?php

//ensures we have cache to test
define('SQL_CACHE_DRIVER', 'files');
define('SQL_CACHE_TIME', 1);
define('DEBUG_SQL_QUERIES', 2);

include_once(__DIR__ . '/../app/config.php');

