<?php
namespace Goteo\Library {

    use Goteo\Core\Exception;

    // Api dependences
    require_once 'library/google/Google_Client.php';  
    require_once 'library/google/contrib/Google_AnalyticsService.php';  

	/*
	 * Clase para usar la API de Google, inicialmente para obtener datos de Analytics
	 */
    class Google {

        const APP_NAME  = 'Goteo-analytics';
        const CLIENT_ID = '179663724496.apps.googleusercontent.com';
        const APP_EMAIL = '179663724496@developer.gserviceaccount.com';
        const PATH_TO_PRIVATE_KEY_FILE = '/var/www/goteo/ga-key/6aace4fc93268addf70cd0339050c4fc94140b9f-privatekey.p12';
        const GA_ACCOUNT = 53209379;


        public static function prepRequest($startDate, $endDate, $metrics = 'ga:visits', $optParams = null) {
            return array(
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'metrics' => $metrics,
                'optParams' => $optParams
            );
        }
        
        public static function getRawData($request) {

        try {
                // create client object and set app name
                $client = new \Google_Client();
                $client->setApplicationName(self::APP_NAME); // name of your app

                // set assertion credentials
                $client->setAssertionCredentials(
                  new \Google_AssertionCredentials(

                    self::APP_EMAIL, // email you added to GA

                    array('https://www.googleapis.com/auth/analytics.readonly'),

                    file_get_contents(self::PATH_TO_PRIVATE_KEY_FILE)  // keyfile you downloaded

                ));

                // other settings
                $client->setClientId(self::CLIENT_ID);           // from API console
                $client->setAccessType('offline_access');  // this may be unnecessary?

                // create service and get data
                $service = new \Google_AnalyticsService($client);
                $service->data_ga->get(self::GA_ACCOUNT, $startDate, $endDate, $metrics, $optParams);                
			}
			catch(Exception $ex) {

                echo 'EXCEPTION AREA!<br />';
                echo $ex->getMessage();
                die;
			}
            
        }
        
        

	}
	
}