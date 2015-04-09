<?php

namespace Goteo\Model\Location;
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
        $city,
        $region,
        $country,
        $country_code,  // codigo pais  ISO 3166-1 alpha-2
        $longitude,
        $latitude,
        $info, //Some stored info
        $name, //friendly description
        $id;

    public function __construct() {
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);
        $this->locable = (bool) $this->locable;
        $this->name = $this->city ? ($this->region ? $this->city . ' (' .$this->region . ')' : $this->city) : ($this->region ? $this->region : $this->country);
    }

    /**
     * Recupera la geolocalización de este
     * @param varcahr(50) $user  user identifier
     * @return UserLocation instance
     */
    public static function get($id) {
        $clas = get_called_class();
        $instance = new $clas;
        $query = static::query("SELECT * FROM " . $instance->Table . " WHERE id = ?", array($id));
        $ob = $query->fetchObject($clas);
        if (!$ob instanceof  $clas) {
            return false;
        }
        return $ob;
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
                        ':method'       => $this->method,
                        ':locable'      => $this->locable,
                        ':info'         => $this->info,
                        ':city'         => $this->city,
                        ':region'       => $this->region,
                        ':country'      => $this->country,
                        ':country_code' => $this->country_code,
                        ':longitude'    => $this->longitude,
                        ':latitude'     => $this->latitude
                        );

        try {
            $sql = "REPLACE INTO " . $this->Table . " (id, method, locable, info, city, region, country, country_code, longitude, latitude) VALUES (:id, :method, :locable, :info, :city, :region, :country, :country_code, :longitude, :latitude)";
            self::query($sql, $values);
        } catch(\PDOException $e) {
            $errors[] = "Error updating location for id. " . $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * Desasignar el usuario de su localización
     *
     * @param array $errors
     * @return boolean
     */
    public function delete (&$errors = array()) {
        try {
            self::query("DELETE FROM " . $this->Table . " WHERE id = ?", array($this->id));
        } catch(\PDOException $e) {
            $errors[] = 'Error removing location for id ' . $this->id . '. ' . $e->getMessage();
            return false;
        }
        return true;
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
        if(property_exists($ob, $prop)) {
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
        if(property_exists($ob, $prop)) {
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
     * Using the spherical law of cosines
     * Based on "Selecting points within a bounding circle"
     *          http://www.movable-type.co.uk/scripts/latlong-db.html
     * @usage
     *
     * $user = User::get('user-id');
     * $location = UserLocation:get($user);
     * //Get users in a 100 Km radius (max: 3)
     * foreach($location->getNearby(100, 0, 3) as $distance => $user_location) {
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
    public function getNearby($distance = 100, $offset = 0, $limit = 10) {
        // empty if no longitude/latitude
        if(is_null($this->latitude) || is_null($this->longitude)) return array();

        // Creating a square "first cut" to not do the calculation over the full table

        $lat = $this->latitude;  // latitude of centre of bounding circle in degrees
        $lon = $this->longitude; // longitude of centre of bounding circle in degrees
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
            ':id'     => $this->id
        );

        $sql = 'SELECT id, latitude, longitude, method, locable, city, region, country, country_code, info, modified,
                ACOS(SIN(:lat)*SIN(RADIANS(latitude)) + COS(:lat)*COS(RADIANS(latitude))*COS(RADIANS(longitude)-:lon)) * :R AS Distance
                FROM (
                    SELECT id, latitude, longitude, method, locable, city, region, country, country_code, info, modified
                    FROM user_location
                    WHERE latitude BETWEEN :minLat AND :maxLat
                      AND longitude BETWEEN :minLon AND :maxLon
                      AND id != :id

                ) AS FirstCut
                WHERE ACOS(SIN(:lat)*SIN(RADIANS(latitude)) + COS(:lat)*COS(RADIANS(latitude))*COS(RADIANS(longitude)-:lon)) * :R < :rad
                ORDER BY Distance
                LIMIT ' . (int) $offset . ',' . (int) $limit;

        $ret = array();
        if($query = $this::query($sql, $params)) {
            foreach ($query->fetchAll(\PDO::FETCH_CLASS, get_called_class()) as $ob) {
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
