<?php

use Goteo\Application\App;
use Goteo\Application\Config;

use Symfony\Component\HttpFoundation\Request;


//Public Web path
define('GOTEO_WEB_PATH', __DIR__ . '/');

require_once __DIR__ . '/../src/autoload.php';

//Get from globals defaults
$request = App::getRequest();

$app = App::get();

$app->run();

