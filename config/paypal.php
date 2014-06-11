<?php
// GENERALES

// si proxy y su configuración
define('USE_PROXY',FALSE);
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
define('API_BASE_ENDPOINT', 'https://svcs.paypal.com/');

// credenciales otorgados por PayPal
define('API_USERNAME', '---');
define('API_PASSWORD', '---');
define('API_SIGNATURE', '--');

// app id paypal
define('X_PAYPAL_APPLICATION_ID','-APP-');

