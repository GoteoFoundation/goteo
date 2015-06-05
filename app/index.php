<?php

use Goteo\Application\GoteoApp;
use Symfony\Component\HttpFoundation\Request;


require_once __DIR__ . '/config.php';

$request = Request::createFromGlobals();


// Routes
$routes = include __DIR__.'/../src/app.php';


$framework = new GoteoApp($routes);

$response = $framework->handle($request);

$response->send();

$framework->terminate();
