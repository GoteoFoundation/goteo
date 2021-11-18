<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

use Goteo\Application\Config;

require_once __DIR__ . '/../src/Goteo/Core/Helpers.php';

$loader = require (__DIR__  . '/../vendor/autoload.php' );

Config::setLoader($loader);

define('GOTEO_PATH', realpath(dirname(__DIR__)) . '/');
define('GOTEO_LOG_PATH', GOTEO_PATH . 'var/logs/');
//Uploads
define('GOTEO_DATA_PATH', GOTEO_PATH . 'var/data/');
define('GOTEO_CACHE_PATH', GOTEO_PATH . 'var/cache/');
