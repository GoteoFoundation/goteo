<?php
define('GOTEO_PATH', __DIR__ . DIRECTORY_SEPARATOR);
if (function_exists('ini_set')) {
    ini_set('include_path', GOTEO_PATH . PATH_SEPARATOR . '.');
} else {
    throw new \Goteo\Core\Exception("No puedo añadir la API GOTEO al include_path.");
}

// quitar magic quotes sin depender del php.ini
if (function_exists('ini_set')) {
    ini_set('magic_quotes_gpc', '0'); 
}

// Nodo actual
define('GOTEO_NODE', 'goteo');

define('PEAR', GOTEO_PATH . 'library' . '/' . 'pear' . '/');
if (function_exists('ini_set')) {
    ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PEAR);
} else {
    throw new \Goteo\Core\Exception("No puedo añadir las librerías PEAR al include_path.");
}

if (!defined('PHPMAILER_CLASS')) {
    define ('PHPMAILER_CLASS', GOTEO_PATH . 'library' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'class.phpmailer.php');
}
if (!defined('PHPMAILER_LANGS')) {
    define ('PHPMAILER_LANGS', GOTEO_PATH . 'library' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR);
}
if (!defined('PHPMAILER_SMTP')) {
    define ('PHPMAILER_SMTP', GOTEO_PATH . 'library' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'class.smtp.php');
}
if (!defined('PHPMAILER_POP3')) {
    define ('PHPMAILER_POP3', GOTEO_PATH . 'library' . DIRECTORY_SEPARATOR . 'phpmailer' . DIRECTORY_SEPARATOR . 'class.pop3.php');
}

// Metadata
define('GOTEO_META_TITLE', 'Goteo.org');
define('GOTEO_META_DESCRIPTION', 'Plataforma social de financiación distribuida');
define('GOTEO_META_KEYWORDS', 'crowdfunding, microcreditos, social, network, financiacion distribuida');
define('GOTEO_META_AUTHOR', 'Onliners');
define('GOTEO_META_COPYRIGHT', 'Platoniq');

// Database
define('GOTEO_DB_DRIVER', 'mysql');
define('GOTEO_DB_HOST', 'localhost');
define('GOTEO_DB_PORT', 3306);
define('GOTEO_DB_SCHEMA', 'goteo');
define('GOTEO_DB_USERNAME', 'goteo');
define('GOTEO_DB_PASSWORD', 'goteo1234');

// Mail
define('GOTEO_MAIL_FROM', 'noreply@goteo.org');
define('GOTEO_MAIL_NAME', 'Goteo.org');
define('GOTEO_MAIL_TYPE', 'mail');
define('GOTEO_MAIL_SMTP_AUTH', true);
define('GOTEO_MAIL_SMTP_SECURE', 'ssl');
define('GOTEO_MAIL_SMTP_HOST', 'mail.goteo.org');
define('GOTEO_MAIL_SMTP_PORT', 465);
define('GOTEO_MAIL_SMTP_USERNAME', 'hola@goteo.org');
define('GOTEO_MAIL_SMTP_PASSWORD', 'goteo1234');

// Language
define('GOTEO_DEFAULT_LANG', 'es');

// url
define('SITE_URL', 'http://devgoteo.org');

/****************************************************
Paypal web_constants.php

Define constants used by web pages in this file
****************************************************/
/* Define the PayPal URL. This is the URL that the buyer is
   first sent to to authorize payment with their paypal account
   change the URL depending if you are testing on the sandbox
   or going to the live PayPal site
   For the sandbox, the URL is
   https://www.sandbox.paypal.com/webscr&cmd=_ap-payment&paykey=
   For the live site, the URL is
   https://www.paypal.com/webscr&cmd=_ap-payment&paykey=
   */
define('PAYPAL_REDIRECT_URL', 'https://www.sandbox.paypal.com/webscr&cmd=');
define('PAYPAL_DEVELOPER_PORTAL', 'https://developer.paypal.com');
define('PAYPAL_DEVICE_ID', 'goteo.org');
define('PAYPAL_APPLICATION_ID', 'APP-80W284485P519543T');
define('PAYPAL_BUSINESS_ACCOUNT', 'goteo_1302553021_biz@gmail.com');
define('PAYPAL_IP_ADDRESS', '127.0.0.1');

/****************************************************
TPV constants
****************************************************/
define('TPV_MERCHANT_CODE', '58857178'); // AMASTE COMUNICACION SL
//define('TPV_REDIRECT_URL', SITE_URL . '/tpv/simulacrum'); // desarrollo
define('TPV_REDIRECT_URL', 'https://sis-t.sermepa.es:25443/sis/realizarPago'); // entorno test
////////////////////define('TPV_REDIRECT_URL', 'https://sis.sermepa.es/sis/realizarPago'); // entorno real
define('TPV_ENCRYPT_KEY', 'qwertyasdf0123456789'); // clave test
/////////////////define('TPV_ENCRYPT_KEY', 'dso8uycgno97syeoi8i6'); // clave real (aun es falsa)
define('TPV_WEBSERVICE_URL', 'https://sis-t.sermepa.es:25443/sis/operaciones'); //pruebas
//define('TPV_WEBSERVICE_URL', 'https://sis.sermepa.es/sis/operaciones'); //real

