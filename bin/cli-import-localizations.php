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
    Goteo\Model\Location,
    Goteo\Model\User\UserLocation,
    Goteo\Model\Project\ProjectLocation;

require_once __DIR__ . '/../app/config.php';

echo "Script para geolocalizar datos de proyectos y usuarios a las tablas location/location_item\n";

$UPDATE = true;
if($argv[1] !== '--update') {
    echo "Usar con el modificador --update para actualizar la base de datos, si no se ejecuta en modo solo lectura\n";
    $UPDATE = false;
}
$LIMIT = 1000;
$CACHE_FILE = __DIR__ . '/cached-location-errors.json';

$errors = @json_decode(@file_get_contents($CACHE_FILE));
if(!is_array($errors)) $errors = array();
echo "\n\nIMPORTACION USUARIOS\n\n";
if($query = Model::query("SELECT user.id,user.location FROM user WHERE user.location!='' AND !ISNULL(user.location) AND location NOT IN ('" . implode("','", $errors) . "') AND user.id NOT IN (SELECT item FROM location_item WHERE type='user') LIMIT $LIMIT")) {
    foreach ($list = $query->fetchAll(\PDO::FETCH_OBJ) as $user) {
        echo "Usuario: {$user->id} Localizacion: [{$user->location}]\n";
        $params = array(
            'sensor' => 'false',
            'address' => $user->location
            );
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params);
        // echo "URL: $url\n";
        $result = json_decode(file_get_contents($url), true);
        if($result['status'] == 'OK') {
            // print_r($result['results'][0]);
            $lat = $result['results'][0]['geometry']['location']['lat'];
            $lng = $result['results'][0]['geometry']['location']['lng'];
            $city = '';
            $country = '';
            $country_code = '';
            $region = '';
            foreach($result['results'][0]['address_components'] as $ob) {
                if($ob['types'][0] === 'country' && $ob['types'][1] === 'political') {
                    $country = $ob['long_name'];
                    $country_code = $ob['short_name'];
                }
                if($ob['types'][0] === 'locality' && $ob['types'][1] === 'political') {
                    $city = $ob['long_name'];
                }
                if(($ob['types'][0] === 'administrative_area_level_1' || $ob['types'][0] === 'administrative_area_level_2') && $ob['types'][1] === 'political') {
                    $region = $ob['long_name'];
                }
            }
            echo "OK, lat,lng: [$lat,$lng] city: [$city] country: [$country_code, $country] region [$region]\n";
            //add user location
            if($UPDATE) {
                if(!UserLocation::addUserLocation(array(
                    'city' => $city,
                    'region' => $region,
                    'country' => $country,
                    'country_code' => $country_code,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'user' => $user->id,
                    'method' => 'manual'
                    ), $err)) {
                    echo "NOT ADDED! Errors: " . print_r($err, 1) . "\n";
                }
            }
        }
        else {
            //write to cache
            echo "ZERO RESULTS, caching...\n";
            $errors[] = str_replace("'", "\\'", $ob->location);
        }
    }
}

echo "\n\nIMPORTACION PROYECTOS\n\n";
if($query = Model::query("SELECT project.id,project.location FROM project WHERE project.location!='' AND !ISNULL(project.location) AND location NOT IN ('" . implode("','", $errors) . "') AND project.id NOT IN (SELECT item FROM location_item WHERE type='project') LIMIT $LIMIT")) {
    foreach ($list = $query->fetchAll(\PDO::FETCH_OBJ) as $project) {
        echo "Usuario: {$project->id} Localizacion: [{$project->location}]\n";
        $params = array(
            'sensor' => 'false',
            'address' => $project->location
            );
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params);
        echo "URL: $url\n";
        $result = json_decode(file_get_contents($url), true);
        if($result['status'] == 'OK') {
            // print_r($result['results'][0]);
            $lat = $result['results'][0]['geometry']['location']['lat'];
            $lng = $result['results'][0]['geometry']['location']['lng'];
            $city = '';
            $country = '';
            $country_code = '';
            $region = '';

            foreach($result['results'][0]['address_components'] as $ob) {
                if($ob['types'][0] === 'country' && $ob['types'][1] === 'political') {
                    $country = $ob['long_name'];
                    $country_code = $ob['short_name'];
                }
                if($ob['types'][0] === 'locality' && $ob['types'][1] === 'political') {
                    $city = $ob['long_name'];
                }
                if(($ob['types'][0] === 'administrative_area_level_1' || $ob['types'][0] === 'administrative_area_level_2') && $ob['types'][1] === 'political') {
                    $region = $ob['long_name'];
                }
            }
            echo "OK, lat,lng: [$lat,$lng] city: [$city] country: [$country_code, $country] region [$region]\n";
            //add project location
            if($UPDATE) {
                if(!ProjectLocation::addProjectLocation(array(
                    'city' => $city,
                    'region' => $region,
                    'country' => $country,
                    'country_code' => $country_code,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'project' => $project->id,
                    'method' => 'manual'
                    ), $err)) {
                    echo "NOT ADDED! Errors: " . print_r($err, 1) . "\n";
                }
            }
        }
        else {
            //write to cache
            echo "ZERO RESULTS, caching...\n";
            $errors[] = str_replace("'", "\\'", $ob->location);
        }
    }
}

file_put_contents($CACHE_FILE, json_encode($errors));
