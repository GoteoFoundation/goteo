<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Model,
		Goteo\Library\Google,
		Goteo\Library\Geoloc,
		Goteo\Library\Text,
		Goteo\Library\Feed;

	class System extends \Goteo\Core\Controller {


        public function analytics () {

            try {

                require_once '../library/google/Google_Client.php';  
                require_once '../library/google/contrib/Google_AnalyticsService.php';  

                // create client object and set app name
                $client = new \Google_Client();
                $client->setApplicationName('Goteo-analytics'); // name of your app

                // set assertion credentials
                $client->setAssertionCredentials(
                  new \Google_AssertionCredentials(

                    '179663724496@developer.gserviceaccount.com', // email you added to GA

                    array('https://www.googleapis.com/auth/analytics.readonly'),

                    file_get_contents('/var/www/goteo/ga-key/6aace4fc93268addf70cd0339050c4fc94140b9f-privatekey.p12')  // keyfile you downloaded

                ));

                // other settings
                $client->setClientId('179663724496.apps.googleusercontent.com');           // from API console
                $client->setAccessType('offline_access');  // this may be unnecessary?

                // create service and get data
                $service = new \Google_AnalyticsService($client);
                $results = $service->data_ga->get(53209379, '2013-06-03', '2013-06-30', 'ga:visits');

                echo \trace($results);
                die;
			}
			catch(Exception $ex) {

                echo 'EXCEPTION!!!<br />';
                echo $ex->getMessage();
                die;
			}
        }

        public function info () {
            phpinfo();
        }

        public function server () {
            die(\trace($_SERVER));
        }

        public function test () {
            
            echo new View('view/system/test.html.php');
        }

        public function whoami () {
            die(\trace($_SESSION['user']));
        }

        public function whereami ($type = '') {
            
            switch ($type) {
                case 'c':
                    // POR COORDENADAS
                    $geoloc = Geoloc::searchLoc(array('latlng'=>"39.588757,-6.05896"));
                    echo \trace($geoloc);
                    break;

                case 'a':
                    //  POR DIRECCION
                    $geoloc = Geoloc::searchLoc(array('address'=>"España"));
                    echo \trace($geoloc);
                    break;
                
                default:
                    echo new View('view/prologue.html.php');
                    echo new View('view/system/geoloc.html.php');
                    echo new View('view/epilogue.html.php');
                    break;
            }
            
            die;
        }

        public function index ($tool = 'index') {

            switch ($tool) {
                case 'lasts':
                    // get lasts 10 users registered
                    $sql = "SELECT id, name, email, confirmed, created
                        FROM user
                        ORDER BY created DESC
                        LIMIT 10
                        ";
                    $query = Model::query($sql);
                    $data = $query->fetchAll(\PDO::FETCH_OBJ);

                    break;

                default:
                    // get lasts 10 users registered
                    $sql = "SELECT id, name, email, confirmed, created
                        FROM user
                        ORDER BY created DESC
                        LIMIT 10
                        ";
                    $query = Model::query($sql);
                    $data = $query->fetchAll(\PDO::FETCH_OBJ);
                    
                    break;
            }

            return new View('view/system/index.html.php', array('data'=>$data));
        }


	}

}
