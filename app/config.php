<?php
define('GOTEO_PATH', __DIR__ . DIRECTORY_SEPARATOR);

// ¿ESTO ESTA OBSOLETO?
// if (function_exists('ini_set')) {
//     ini_set('include_path', GOTEO_PATH . PATH_SEPARATOR . '.');
// } else {
//     throw new Exception("No puedo añadir la API GOTEO al include_path.");
// }


// define('PEAR', GOTEO_PATH . 'library' . '/' . 'pear' . '/');
// if (function_exists('ini_set')) {
//     ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PEAR);
// } else {
//     throw new Exception("No puedo añadir las librerías PEAR al include_path.");
// }


/******************************************************
PhpFastCache constants
*******************************************************/
if (!defined('PHPFASTCACHE_CLASS')) {
    define ('PHPFASTCACHE_CLASS', GOTEO_PATH . 'library' . DIRECTORY_SEPARATOR . 'phpfastcache' . DIRECTORY_SEPARATOR . 'phpfastcache.php');
}

/******************************************************
OAUTH APP's Secrets
*******************************************************/
if (!defined('OAUTH_LIBS')) {
    define ('OAUTH_LIBS', GOTEO_PATH . 'library' . DIRECTORY_SEPARATOR . 'oauth' . DIRECTORY_SEPARATOR . 'SocialAuth.php');
}

//Uploads
define('GOTEO_DATA_PATH', __DIR__ . '/data/');
//cache
define('GOTEO_CACHE_PATH', __DIR__ . '/../var/cache/');

/**
 * Carga de configuración local si existe
 * Si no se carga el real (si existe)
**/
$config_file = __DIR__ . '/../config/settings.php';
if (file_exists($config_file)) { //en .gitignore
    require $config_file;
} else {
    $demo_config_file = __DIR__ . '/../config/demo-settings.php';
    die('<h2>No se encuentra el archivo de configuraci&oacute;n <code><strong>config/settings.php</strong></code>, debes crear este archivo en el subdirectorio config/.</h2><p>Puedes usar el siguiente c&oacute;digo modificado con los credenciales adecuados.</p>' . highlight_string(file_get_contents($demo_config_file), true) );
}
