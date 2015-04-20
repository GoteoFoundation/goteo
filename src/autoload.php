<?php

//Composer packages
$loader = require (__DIR__  . '/../vendor/autoload.php' );
// TODO:
// una entrada por plugin...
// ...
$loader->add('', __DIR__ . '/../extend/goteo/src');

//Cache dir in libs
\Goteo\Library\Cacher::setCacheDir(GOTEO_CACHE_PATH);

// Error handler
set_error_handler (

    function ($errno, $errstr, $errfile, $errline, $errcontext) {
        // @todo Insert error into buffer
//        echo "Error:  {$errno}, {$errstr}, {$errfile}, {$errline}, {$errcontext}<br />";
        //throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

);
//
