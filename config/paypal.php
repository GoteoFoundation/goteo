<?php
// OBSOLETO desde 09/12/2014
// con vendor/paypal la configuración va en config/sdk_config.ini
// ahí cada desarrollador tiene que poner los credenciales generados en su panel paypal.developer para su entorno sandbox
die;
/* HERE AN EXAMPLE OF sdk_config.ini functional content
 * see more about configuration at https://github.com/paypal/sdk-core-php/wiki/Configuring-the-SDK#examples
 *
;
; Full explained paypal config file
;
;

; 'sandbox' or 'live'.
mode = sandbox

;Account credentials
[Account]

; Credentials for 3-token authentication
acct1.UserName = jb-us-seller_api1.paypal.com
acct1.Password = WX4WTU3S8MY44S7F
acct1.Signature = AFcWxV21C7fd0v3bYYYRCpSSRl31A7yDhhsPUU2XhtMoZXsWHFxu-RWy
acct1.AppId = APP-80W284485P519543T
# Subject is optional and is required only in case of third party authorization
acct1.Subject =

; Certificate Credentials Test Account
;  for client certificate authentication.
;acct2.UserName = platfo_1255170694_biz_api1.gmail.com
;acct2.Password = 2DPPKUPKB7DQLXNR
; Certificate path relative to config folder or absolute path in file system
;acct2.CertPath = cacert.pem / resource/sdk-cert.p12 / etc..


;Connection Information
[Http]

; Connection timeout (seconds. Default 30)
http.ConnectionTimeOut = 60

; Number of retries for connection errors (defaults to 5 if not specified)
;http.Retry = 1

; Example: [username:password@]proxyIP[:port]
;http.Proxy


;Logging Information
[Log]

; Absolute path to log file
log.FileName=PayPal.log

; Can be one of FINE, INFO, WARN or ERROR
log.LogLevel=INFO

; Enable logging
log.LogEnabled=true

 */

/////////////////////////////////
////// OBSOLETE FROM HERE ON   //
/////////////////////////////////

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
