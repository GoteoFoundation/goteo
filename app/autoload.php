<?php

//Composer packages
$loader = require (__DIR__  . '/../vendor/autoload.php' );
// TODO:
// una entrada por plugin...
// ...
$loader->add('', __DIR__ . '/../extend/goteo/src');

// Legacy Autoloader
spl_autoload_register(

    function ($cls) {

        $file = __DIR__ . '/' . implode('/', explode('\\', strtolower(substr($cls, 6)))) . '.php';
        $file = realpath($file);

        if ($file === false) {

            // Try in library
//            $file = __DIR__ . '/library/' . implode('/', explode('\\', strtolower(substr($cls, 6)))) . '.php';
            $file = __DIR__ . '/library/' . strtolower($cls) . '.php';
//            die($cls . ' - ' . $file); //Si uso Text::get(id) no lo pilla
        }

        if ($file !== false) {
            include $file;
        }

    }

);


// Error handler
set_error_handler (

    function ($errno, $errstr, $errfile, $errline, $errcontext) {
        // @todo Insert error into buffer
//        echo "Error:  {$errno}, {$errstr}, {$errfile}, {$errline}, {$errcontext}<br />";
        //throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

);
//
