<?php

use Goteo\Application\App;
use Symfony\Component\HttpFoundation\Request;


error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set("display_errors",1);

//Public Web path
define('GOTEO_WEB_PATH', __DIR__ . '/');

require_once __DIR__ . '/../src/autoload.php';

App::debug(true);

//Get from globals defaults
$request = App::getRequest();

$app = App::get();

$app->run();

