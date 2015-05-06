<?php

//ensures we have cache to test
define('SQL_CACHE_TIME', 1);
define('DEBUG_SQL_QUERIES', 2);


include_once(__DIR__ . '/../app/config.php');

//some views uses it
define('NODE_ID', GOTEO_NODE);
define('SITE_URL', '//localhost');
define('LANG', 'es');
define('HTTPS_ON', false);

include_once(__DIR__ . '/../src/defaults.php');

