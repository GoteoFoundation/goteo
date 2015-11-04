<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */


// Helpers
require_once __DIR__ . '/../src/Goteo/Core/Helpers.php';

//Composer packages
$loader = require (__DIR__  . '/../vendor/autoload.php' );

\Goteo\Application\Config::setLoader($loader);

//Main path
define('GOTEO_PATH', realpath(dirname(__DIR__)) . '/');
//Log path
define('GOTEO_LOG_PATH', GOTEO_PATH . 'var/logs/');
//Uploads
define('GOTEO_DATA_PATH', GOTEO_PATH . 'var/data/');
//cache
define('GOTEO_CACHE_PATH', GOTEO_PATH . 'var/cache/');


