<?php
/**
* Este es el proceso que va procesando envios masivos
* version linea de comandos
**/

if (PHP_SAPI !== 'cli') {
    die("Acceso solo por linea de comandos!");
}
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set("display_errors",1);
//system timezone
date_default_timezone_set("Europe/Madrid");

use Goteo\Core\Resource,
    Goteo\Core\Error,
    Goteo\Core\Model,
    Goteo\Application\Config,
    Goteo\Model\Location,
    Goteo\Library\Cacher,
    Goteo\Model\User\UserLocation,
    Goteo\Util\Google\GoogleGeocoder,
    Goteo\Model\Project\ProjectLocation;

//Public Web path
define('GOTEO_WEB_PATH', dirname(__DIR__) . '/app/');

require_once __DIR__ . '/../src/autoload.php';

// Config file...
Config::loadFromYaml('settings.yml');

echo "This script geolocates Users & Projects into the tables location/user_location/project_location\n";

//Google Api day limit
$GOOGLE_LIMIT = 2500;
//Waiting Hours
$GOOGLE_TIME = 24;

$UPDATE = true;
if($argv[1] !== '--update') {
    echo "Use the --update modified to actually update the database\n";
    $UPDATE = false;
}
$LIMIT = 1;
$cache = new Cacher('geocoder-import');
$CACHE_FILE = $cache->getFile('cached-location-errors.json');
$api_cache_key = $cache->getKey('num-api-calls');
$num_api_calls = (int) $cache->retrieve($api_cache_key);
$key_data = unserialize(file_get_contents($api_cache_key->getFileName()));
//How long will be valid this period
$TTL = $GOOGLE_TIME * 3600;
if(!$num_api_calls) {
    echo "RESET KEY\n";
    $cache->store($api_cache_key, $num_api_calls, $TTL);
    $key_data = unserialize(file_get_contents($api_cache_key->getFileName()));
}
// print_r($key_data);
echo "Key created at [" . date("Y-m-d H:i:s", $key_data->created) . "]\n";
echo "Key expires at [" . date("Y-m-d H:i:s", $key_data->expires) . "]\n";

if($num_api_calls > $GOOGLE_LIMIT) {
    echo "GOOGLE API CALLS LIMIT REACHED!\n";
    echo "Please retry in " . round(($key_data->expires - time())/3600,2) . " hours\n";
    die("Bye!\n");
}
else {
    echo "Last {$GOOGLE_TIME}h number of Google Api Calls: [$num_api_calls], hours remaining for this period: [" . round(($key_data->expires - time())/3600, 2) . "]\n";
}

$errors = @json_decode(@file_get_contents($CACHE_FILE));
if(!is_array($errors)) $errors = array();
/*
echo "\nIMPORTING USERS\n\n";
if($query = Model::query("SELECT user.id,user.location FROM user WHERE user.location!='' AND !ISNULL(user.location) AND user.location NOT IN ('" . implode("','", $errors) . "') AND user.id NOT IN (SELECT user FROM user_location) LIMIT $LIMIT")) {
    foreach ($list = $query->fetchAll(\PDO::FETCH_OBJ) as $user) {
        echo "USER: {$user->id} LOCATION: [{$user->location}]\n";
        if($data = GoogleGeocoder::getCoordinates(array('address' => $user->location))) {

            echo "GEOLOCATED: lat,lng: [{$data['latitude']},{$data['longitude']}] city: [{$data['city']}] country: [{$data['country_code']}, {$data['country']}] region [{$data['region']}]";
            //add user location
            if($UPDATE) {
                echo " UPDATING:";
                $loc = new UserLocation($data + array(
                    'id' => $user->id,
                    'method' => 'manual'
                    ));
                if($loc->save($err)) {
                    echo " OK";
                }
                else {
                    echo " FAILED! Errors: \n" . print_r($err, 1);
                }
            }
            else echo " DUMMY";
            echo "\n";
        }
        else {
            //write to cache
            echo "ZERO RESULTS, caching...\n";
            $errors[] = str_replace("'", "\\'", $user->location);
        }

        $cache->modify( $api_cache_key,
                        function () use ($num_api_calls) {
                            return $num_api_calls + 1;
                        }
                    );

    }
}
*/
echo "\nIMPORTING PROJECTS\n\n";
if($query = Model::query("SELECT project.id,project.project_location AS location FROM project WHERE project.project_location!='' AND !ISNULL(project.project_location) AND project.project_location NOT IN ('" . implode("','", $errors) . "') AND project.id NOT IN (SELECT project FROM project_location) LIMIT $LIMIT")) {
    foreach ($list = $query->fetchAll(\PDO::FETCH_OBJ) as $project) {
        echo "PROJECT: {$project->id} LOCATION: [{$project->location}]\n";
        if($data = GoogleGeocoder::getCoordinates(array('address' => $project->location))) {

            echo "GEOLOCATED: lat,lng: [{$data['latitude']},{$data['longitude']}] city: [{$data['city']}] country: [{$data['country_code']}, {$data['country']}] region [{$data['region']}]";
            //add project location
            if($UPDATE) {
                echo " UPDATING:";
                $loc = new ProjectLocation($data + array(
                    'project' => $project->id,
                    'method' => 'manual'
                    ));
                if($loc->save($err)) {
                    echo " OK";
                }
                else {
                    echo " FAILED! Errors: \n" . print_r($err, 1);
                }
            }
            else echo " DUMMY";
            echo "\n";
        }
        else {
            //write to cache
            echo "ZERO RESULTS, caching...\n";
            $errors[] = str_replace("'", "\\'", $project->location);
        }

        $cache->modify( $api_cache_key,
                        function () use ($num_api_calls) {
                            return $num_api_calls + 1;
                        }
                    );

    }
}

file_put_contents($CACHE_FILE, json_encode($errors));
