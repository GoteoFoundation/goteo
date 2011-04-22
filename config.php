<?php

define('GOTEO_PATH', __DIR__ . DIRECTORY_SEPARATOR);
// Database
define('GOTEO_DB_DRIVER', 'mysql');
define('GOTEO_DB_HOST', 'localhost');
define('GOTEO_DB_PORT', 3306);
define('GOTEO_DB_SCHEMA', 'goteo');
define('GOTEO_DB_USERNAME', 'goteo');
define('GOTEO_DB_PASSWORD', 'goteo1234');


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
define('DEVELOPER_PORTAL', 'https://developer.paypal.com');
define('DEVICE_ID', 'goteo.org');
define('APPLICATION_ID', 'APP-80W284485P519543T');
