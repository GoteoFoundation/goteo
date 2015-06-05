<?php

use Goteo\Application\GoteoApp;
use Symfony\Component\HttpFoundation\Request;

//Public Web path
define('GOTEO_WEB_PATH', __DIR__ . '/');

require_once __DIR__ . '/../src/autoload.php';

$request = Request::createFromGlobals();


// Routes
$routes = include __DIR__.'/../src/app.php';


$framework = new GoteoApp($routes);

$response = $framework->handle($request);

$response->send();

$framework->terminate();
