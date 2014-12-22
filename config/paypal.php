<?php
// GENERALES

// si proxy y su configuración
define('USE_PROXY',false);
// define('PROXY_HOST', '127.0.0.1');
// define('PROXY_PORT', '808');

// true solo durante implementeación. false para testear la implementación
define('TRUST_ALL_CONNECTION',false);

// versión del SDK
define('SDK_VERSION','PHP_SOAP_SDK_V1.4');

// formato de la comunicación
define('X_PAYPAL_REQUEST_DATA_FORMAT','JSON');
define('X_PAYPAL_RESPONSE_DATA_FORMAT','JSON');

// ip interna del servidor
define('X_PAYPAL_DEVICE_IPADDRESS','127.0.0.1');

// tipo de autenticación
define('API_AUTHENTICATION_MODE','3token');

// SEGUN ENTORNO
// Endpoint
define('API_BASE_ENDPOINT', 'https://svcs.sandbox.paypal.com/');
//Chanege to https://svcs.paypal.com/  to go live */

// credenciales
define('API_USERNAME', 'goteo_1314917819_biz_api1.gmail.com');
define('API_PASSWORD', '1314917863');
define('API_SIGNATURE', 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-A0jpddAN2GLAUit7Ii3-bZGjjerJ');

// app id
define('X_PAYPAL_APPLICATION_ID','APP-80W284485P519543T');
