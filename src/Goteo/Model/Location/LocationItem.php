<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Model\Location;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Config;

/**
 * This class can be used to easily create a location item model just as:
 *
 * class UserLocation extends \Goteo\Model\Location\LocationItem {
 *     protected $Table = 'user_location'; //MySQL table
 * }
 *
 * Then all basic methods of the interface will be implemented
 */
abstract class LocationItem extends \Goteo\Core\Model implements LocationInterface  {
    protected $Table; // this needs to be overwritten (MySQL Table) by the implementation
    public
        $method, // latitude,longitude obtaining method
                 // ip      = auto detection from ip,
                 // browser = automatic provided by browser,
                 // manual    = manually provided by any method
        $locable = true, //if is or not locable
        $location,
        $city,
        $region,
        $country,
        $country_code,  // codigo pais  ISO 3166-1 alpha-2
        $longitude,
        $latitude,
        $radius,
        $info, //Some stored info
        $name, //friendly description
        $id;

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        $this->locable = (bool) $this->locable;
        // alternative location names
        if(empty($this->city) && $this->location) $this->city = $this->location;
        $this->name = $this->getFormatted();
        $this->radius = (int) $this->radius;
    }

    /**
     * Recupera la geolocalización de este
     * @param varcahr(50) $user  user identifier
     * @return UserLocation instance
     */
    static public function get($id) {
        if(is_object($id)) {
            throw new ModelException("Location identifier [$id] must be a integer or string!");
        }
        $clas = get_called_class();
        $instance = new $clas;
        $query = static::query("SELECT * FROM " . $instance->Table . " WHERE id = ?", array($id));
        $ob = $query->fetchObject($clas);
        if (!$ob instanceof  $clas) {
            return false;
        }
        return $ob;
    }

    /**
     * Creates a new location
     * @param  [type] $ip    [description]
     * @param  LocationInterface $model
     * @return [type]        [description]
     */
    static public function createByIp($id, $ip){
        $cities = Config::get('geolocation.maxmind.cities');
        try {
            // This creates the Reader object, which should be reused across lookups.
            $reader = new \GeoIp2\Database\Reader($cities);
            $record = $reader->city($ip);
            //Handles user localization
            $loc = new static(array(
                    'id'           => $id,
                    'city'         => $record->city->name,
                    'region'       => $record->mostSpecificSubdivision->name,
                    'country'      => $record->country->name,
                    'country_code' => $record->country->isoCode,
                    'longitude'    => $record->location->longitude,
                    'latitude'     => $record->location->latitude,
                    'radius'       => 0,
                    'method'       => 'ip'
                ));

            return $loc;

        }catch(\Exception $e){
            // throw new ModelException('Locator error: ' . $e->getMessage());
        }
        return false;
    }

    public function validate(&$errors = array()) {
        if (empty($this->id)) {
            $errors[] = 'ID missing!';
        }
        $methods = array('ip', 'browser', 'manual');
        if (!in_array($this->method, $methods)) {
            $errors[] = 'Method (' . $this->method . ') error! must be one of: ' . implode(', ', $methods);
        }
        if (empty($this->country_code)) {
            $errors[] = 'Country code missing';
        }
        if (empty($this->country)) {
            $errors[] = 'Country missing';
        }
        if (empty($this->longitude)) {
            $errors[] = 'Longitude missing';
        }
        if (empty($this->latitude)) {
            $errors[] = 'Latitude missing';
        }
        if (empty($errors)) {
            return true;
        }
        else {
            return false;
        }
    }
    /*
     *  Guarda la asignación del usuario a la localización
     */
    public function save (&$errors = array()) {
        if (!$this->validate($errors)) {
            return false;
        }

        $values = array(':id'         => $this->id,
                        ':method'       => (string)$this->method,
                        ':locable'      => (string)$this->locable,
                        ':info'         => (string)$this->info,
                        ':city'         => (string)$this->city,
                        ':region'       => (string)$this->region,
                        ':country'      => (string)$this->country,
                        ':country_code' => $this->country_code,
                        ':longitude'    => $this->longitude,
                        ':latitude'     => $this->latitude,
                        ':radius'       => $this->radius
                        );

        try {
            $sql = "REPLACE INTO " . $this->Table . " (id, method, locable, info, city, region, country, country_code, longitude, latitude, radius) VALUES (:id, :method, :locable, :info, :city, :region, :country, :country_code, :longitude, :latitude, :radius)";
            // die(\sqldbg($sql, $values));
            self::query($sql, $values);
        } catch(\PDOException $e) {
            $errors[] = "Error updating location for id. " . $e->getMessage();
            return false;
        }
        return true;
    }

    public function getFormatted($latlng = false) {
        $txt = $this->city;
        if($this->region && $this->region !== $this->city) $txt .= ', ' .$this->region;
        if($this->country) $txt .= ', ' .$this->country;
        if($latlng) {
            $txt .= ' (' . \euro_format($this->latitude, 2) . 'º / ' . \euro_format($this->longitude, 2) . 'º)';
        }
        return $txt;
    }

    /**
     * Sets a property
     * @param [type] $id    [description]
     * @param [type] $prop    [description]
     * @param [type] $value   [description]
     * @param [type] &$errors [description]
     */
    public static function setProperty($id, $prop, $value, &$errors = array()) {
        $clas = get_called_class();
        $ob = $clas::get($id);
        if($ob && property_exists($ob, $prop)) {
            $ob->$prop = $value;
            return $ob->save($errors);
        }
        else {
            $errors[] = "Property [$prop] does not exists in class [" . $clas . "]";
        }
        return false;
    }

    /**
     * Sets a property
     * @param [type] $id    [description]
     * @param [type] $prop    [description]
     * @param [type] $value   [description]
     * @param [type] &$errors [description]
     */
    public static function getProperty($id, $prop, &$errors = array()) {
        $clas = get_called_class();
        $ob = $clas::get($id);
        if($ob && property_exists($ob, $prop)) {
            return $ob->$prop;
        }
        else {
            $errors[] = "Property [$prop] does not exists in class [" . $clas . "]";
        }
        return null;
    }

    /**
     * Borrar de unlocable
     *
     * @param varchar(50) $id id de un usuario
     * @param array $errors
     * @return boolean
     */
    public static function setLocable ($id, &$errors = array()) {
        return self::setProperty($id, 'locable', 1, $errors);
    }

    /**
     * Añadir a unlocable
     *
     * @param varchar(50) $id id de un usuario
     * @param array $errors
     * @return boolean
     */
    public static function setUnlocable ($id, &$errors = array()) {
        return self::setProperty($id, 'locable', 0, $errors);
    }

    /**
     * Si está como ilocalizable
     * @param varcahr(50) $id  id identifier
     * @return int (have an unlocable register)
     */
    public static function isUnlocable ($id) {
        return !(bool) self::getProperty($id, 'locable');
    }

    /**
     * Returns Location instances ordered by proximity
     *
     * @usage
     *
     * // getting user near 'user-id':
     *
     * $location = UserLocation::get(User::get('user-id'));
     * // Or $location = UserLocation::get('user-id');
     *
     * //Get users in a 100 Km radius (max: 3)
     * foreach($location->getSibilingsNearby(100, 0, 3) as $distance => $user_location) {
     *     $user = User::get($user_location->id);
     *     echo "User: " . $user->username . ", Distance: " . round($distance, 2) . "Km";
     * }
     *
     *
     * @param  integer $distance radius of bounding circle in kilometers
     * @param  integer $offset Offset for MySQL table limit
     * @param  integer $limit Limit for MySQL table limit
     * @return array             Array of LocationItem instances
     */
    public function getSibilingsNearby($distance = 100, $offset = 0, $limit = 10) {
        return $this::getNearby($this, $distance, $offset, $limit);
    }

    /**
     * Returns Location instances ordered by proximity
     *
     * Using the spherical law of cosines
     * Based on "Selecting points within a bounding circle"
     *          http://www.movable-type.co.uk/scripts/latlong-db.html
     *
     * @usage
     *
     * // Getting Projects near User 'user-id'
     *
     * $user_location = UserLocation::get('user-id');
     * //Get projects in a 100 Km radius (max: 3)
     * foreach(ProjectLocation::getNearby($user_location, 100, 0, 3) as $distance => $project_location) {
     *     $project = Project::get($project_location->id);
     *     echo "Project: " . $project->name . ", Distance: " . round($distance, 2) . "Km";
     * }
     *
     *
     * @param  integer $distance radius of bounding circle in kilometers
     * @param  integer $offset Offset for MySQL table limit
     * @param  integer $limit Limit for MySQL table limit
     * @return array             Array of LocationItem instances
     */

    public static function getNearby(LocationInterface $location, $distance = 100, $offset = 0, $limit = 10, $locable_only = true) {
        // empty if no longitude/latitude
        if(is_null($location->latitude) || is_null($location->longitude)) return array();

        // Creating a square "first cut" to not do the calculation over the full table

        $lat = $location->latitude;  // latitude of centre of bounding circle in degrees
        $lon = $location->longitude; // longitude of centre of bounding circle in degrees
        $R   = 6371;             // earth's mean radius, km

        // first-cut bounding box (in degrees)
        $maxLat = $lat + rad2deg($distance/$R);
        $minLat = $lat - rad2deg($distance/$R);
        // compensate for degrees longitude getting smaller with increasing latitude
        $maxLon = $lon + rad2deg($distance/$R/cos(deg2rad($lat)));
        $minLon = $lon - rad2deg($distance/$R/cos(deg2rad($lat)));

        $params = array(
            ':lat'    => deg2rad($lat),
            ':lon'    => deg2rad($lon),
            ':minLat' => $minLat,
            ':minLon' => $minLon,
            ':maxLat' => $maxLat,
            ':maxLon' => $maxLon,
            ':rad'    => $distance,
            ':R'      => $R,
            ':id'     => $location->id
        );

        $clas = get_called_class();
        $instance = new $clas;
        $table = $instance->getTable();
        $firstCut = "SELECT id, latitude, longitude, method, locable, city, region, country, country_code, info, modified
                    FROM $table
                    WHERE latitude BETWEEN :minLat AND :maxLat
                      AND longitude BETWEEN :minLon AND :maxLon";
        if($locable_only) {
            $firstCut .= ' AND locable = 1';
        }
        if(get_class($location) === $clas) {
            $firstCut .= ' AND id != :id';
        }

        $sql = "SELECT id, latitude, longitude, method, locable, city, region, country, country_code, info, modified,
                ACOS(SIN(:lat)*SIN(RADIANS(latitude)) + COS(:lat)*COS(RADIANS(latitude))*COS(RADIANS(longitude)-:lon)) * :R AS Distance
                FROM ($firstCut) AS FirstCut
                WHERE ACOS(SIN(:lat)*SIN(RADIANS(latitude)) + COS(:lat)*COS(RADIANS(latitude))*COS(RADIANS(longitude)-:lon)) * :R < :rad
                ORDER BY Distance
                LIMIT $offset,$limit";
        // echo $sql;
        $ret = array();

        if($query = $clas::query($sql, $params)) {
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, $clas) as $ob) {
                $key = (float)$ob->Distance;
                while(array_key_exists((string)$key, $ret)) {
                    $key = (float)$key + 0.001;
                }
                $ret[(string)$key] = $ob;
            }
        }

        return $ret;
    }
}
