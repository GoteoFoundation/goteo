<?php
define('GOTEO_PATH', __DIR__ . DIRECTORY_SEPARATOR);
if (function_exists('ini_set')) {
    ini_set('include_path', GOTEO_PATH . PATH_SEPARATOR . '.');
} else {
    throw new Exception("No puedo añadir la API GOTEO al include_path.");
}

// Esto no sirve para quitar magic quotes. Ver php.net/manual/en/security.magicquotes.disabling.php
// Tampoco lo he podido quitar a nivel de htacces, sale php.ini
/*
if (function_exists('ini_set')) {
    if (ini_set('magic_quotes_gpc', '0') === false) {
        phpinfo();
        die;
        throw new Exception("No puedo quitar las magic quotes");
    }
} else {
    throw new Exception("no hay ini_set");
}
 *
 */

//Estoy en mantenimiento
define('GOTEO_MAINTENANCE', null);

//Estoy en alto rendimiento
define('GOTEO_EASY', null);

// Nodo actual
define('GOTEO_NODE', 'goteo');

define('PEAR', GOTEO_PATH . 'library' . '/' . 'pear' . '/');
if (function_exists('ini_set')) {
    ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . PEAR);
} else {
    throw new Exception("No puedo añadir las librerías PEAR al include_path.");
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
define('GOTEO_META_TITLE', 'Goteo.org  Crowdfunding the commons');
define('GOTEO_META_DESCRIPTION', utf8_encode('Red social de financiación colectiva'));
define('GOTEO_META_KEYWORDS', utf8_encode('crowdfunding, procomun, commons, social, network, financiacion colectiva, cultural, creative commons, proyectos abiertos, open source, free software, licencias libres'));
define('GOTEO_META_AUTHOR', 'Onliners Web Development');
define('GOTEO_META_COPYRIGHT', 'Platoniq');

// Database
define('GOTEO_DB_DRIVER', 'mysql');
define('GOTEO_DB_HOST', 'localhost');
define('GOTEO_DB_PORT', 3306);
define('GOTEO_DB_CHARSET', 'UTF-8');
define('GOTEO_DB_SCHEMA', 'goteo');
define('GOTEO_DB_USERNAME', 'goteo');
define('GOTEO_DB_PASSWORD', 'g0t3012');

//Uploads i catxe
define('GOTEO_DATA_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR);

// Mail
define('GOTEO_MAIL_FROM', 'noreply@goteo.org');
define('GOTEO_MAIL_NAME', 'Goteo.org');
define('GOTEO_MAIL_TYPE', 'smtp');
define('GOTEO_MAIL_SMTP_AUTH', true);
define('GOTEO_MAIL_SMTP_SECURE', 'tls');
define('GOTEO_MAIL_SMTP_HOST', 'email-smtp.us-east-1.amazonaws.com');
define('GOTEO_MAIL_SMTP_PORT', 587);
define('GOTEO_MAIL_SMTP_USERNAME', 'AKIAIB4NK7M6VJPJ3GWA');
define('GOTEO_MAIL_SMTP_PASSWORD', 'AkJ7j1QgxvgyjCR9/bHxtSh2f2yE0MNFBiVCciB92ifn');

define('GOTEO_MAIL', 'hola@goteo.org');
define('GOTEO_CONTACT_MAIL', 'info@goteo.org');

//Quota de envio máximo para goteo en 24 horas
define('GOTEO_MAIL_QUOTA', 50000);
//Quota de envio máximo para newsletters para goteo en 24 horas
define('GOTEO_MAIL_SENDER_QUOTA', round(GOTEO_MAIL_QUOTA * 0.8));

// Language
define('GOTEO_DEFAULT_LANG', 'es');

// url
define('SITE_URL', 'http://goteo.org');
#define('SITE_URL', 'http://ns235942.ovh.net');
#define('SRC_URL', 'http://ns235942.ovh.net');

// Cron params
define('CRON_PARAM', '4dTJYNfPovGqyMt');
define('CRON_VALUE', 'HsIv6aG36ek2s7Q');


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
define('PAYPAL_REDIRECT_URL', 'https://www.paypal.com/webscr&cmd=');
define('PAYPAL_DEVELOPER_PORTAL', 'https://developer.paypal.com');
define('PAYPAL_DEVICE_ID', 'goteo.org');
define('PAYPAL_APPLICATION_ID', 'APP-4FW639590X463293E');
define('PAYPAL_BUSINESS_ACCOUNT', 'paypal@goteo.org');
define('PAYPAL_IP_ADDRESS', '127.0.0.1');

/****************************************************
TPV constants
****************************************************/
define('TPV_MERCHANT_CODE', '079216792'); // Fundación Fuentes Abiertas
define('TPV_REDIRECT_URL', 'https://pgw.ceca.es/cgi-bin/tpv');
define('TPV_ENCRYPT_KEY', '83074958');

/******************************************************
OAUTH APP's Secrets
*******************************************************/
if (!defined('OAUTH_LIBS')) {
    define ('OAUTH_LIBS', GOTEO_PATH . 'library' . DIRECTORY_SEPARATOR . 'oauth' . DIRECTORY_SEPARATOR . 'SocialAuth.php');
}

//Facebook (l'app de Facebook la té l'usuari ivan@microstudi.net a Facebook)
/*
//facebook app Julian
define('OAUTH_FACEBOOK_ID', '189133314484241'); //
define('OAUTH_FACEBOOK_SECRET', 'f557c5ef0daa83a36bde55807d466d00'); //
*/
//*
//facebook app Ivan
define('OAUTH_FACEBOOK_ID', '184483011630708'); //
define('OAUTH_FACEBOOK_SECRET', '3ecdf6b61b43823f70fefd7b4a77378b'); //


//Twitter (l'app de Twitter la té l'usuari goteofunding a Twitter)
define('OAUTH_TWITTER_ID', 'fO2A3Kx5i2zv4npTUFWWKQ'); //
define('OAUTH_TWITTER_SECRET', 'JfMdtLhGgxx4z6aKiZJ6Pk2wmLlPly3bUohkP6U9zo'); //

//Linkedin (l'app de LinkedIn la té l'usuari ivan@microstudi.net a LinkedIn)
define('OAUTH_LINKEDIN_ID', 'xtmfiu6onthw'); //
define('OAUTH_LINKEDIN_SECRET', 'nNFLjxt1dY6NvuMY'); //


//Un secreto inventado cualquiera para encriptar los emails que sirven de secreto en openid
define('OAUTH_OPENID_SECRET','CjFap3Ow4HJvUahAjWZ8kQ==');

// recaptcha
define('RECAPTCHA_PUBLIC_KEY','6LcnLOcSAAAAALIipxqC0kcKA8v5maiNvh5pMDJ6');
define('RECAPTCHA_PRIVATE_KEY','6LcnLOcSAAAAAM3fpJEYR-03ukTNMd21nLBZUrTr');
