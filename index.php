<?php

session_start();

use Goteo\Core\Error,
    Goteo\Core\Redirection;

require_once 'config.php';

// Include path
set_include_path(GOTEO_PATH . PATH_SEPARATOR . '.');

// Autoloader
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
            include_once $file;
        }
    
    }
    
);

// Error handler
set_error_handler (
        
    function ($errno, $errstr, $errfile, $errline, $errcontext) {
    
        // Insert error into buffer
        
    }
    
);

// Get URI without query string
$uri = strtok($_SERVER['REQUEST_URI'], '?');

// Get requested segments
$segments = preg_split('!\s*/+\s*!', $uri, -1, \PREG_SPLIT_NO_EMPTY);

try {  
    
    // Get controller name    
    if (!empty($segments) && class_exists("Goteo\\Controller\\{$segments[0]}")) {
        // Take first segment as controller        
        $controller = array_shift($segments);        
    } else {
        $controller = 'index';
    }
        
    // Continue
    try {
        
        $class = new ReflectionClass("Goteo\\Controller\\{$controller}");
        
        if (!empty($segments) && $class->hasMethod($segments[0])) {
            $method = array_shift($segments);                        
        } else {
            // Try default method
            $method = 'index';            
        }         
        
        // ReflectionMethod
        $method = $class->getMethod($method);
        
        // Number of params defined in method
        $numParams = $method->getNumberOfParameters(); 
        // Number of required params
        $reqParams = $method->getNumberOfRequiredParameters();        
        // Given params
        $gvnParams = count($segments);
                
        if ($gvnParams >= $reqParams && (!($gvnParams > $numParams && $numParams <= $reqParams))) {
            
            // Try to instantiate
            $instance = $class->newInstance();

            // Start output buffer
            ob_start();

            // Invoke method
            $method->invokeArgs($instance, $segments);
            
            // Get buffer contents
            $buffer = \ob_get_clean();
            
            // Output buffer
            echo $buffer;

            // Farewell            
            die;      
            
        }        
        
    } catch (\ReflectionException $e) {}
    
    throw new Error(Error::NOT_FOUND);    
    
} catch (Redirection $redirection) {
    $url = $redirection->getURL();
    $code = $redirection->getCode();
    header("Location: {$url}");
    
} catch (Error $error) {
    
    include "view/error.html.php";
    
} catch (Exception $exception) {
    
    // Default error (500)    
    include "view/error.html.php";
}