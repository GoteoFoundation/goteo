<?php

/*
 * Este modelo es para la geo localizacion
 *
 * Usamos la libreria geoloc para todas las funcionalidades de webservices
 *
 */

namespace Goteo\Model {

    class Location extends \Goteo\Core\Model {

        public
            $id,
            $name,
            $city,
            $region,
            $country,
            $country_code,  // codigo pais  ISO 3166-1 alpha-2
            $longitude,
            $latitude,
            $valid = 1;

        /*
         * Conteo de usuarios por geolocalización
         */
        public static function countBy ($type = 'user', $count = 'registered', $keyword = '') {

            switch ($count) {
                case 'located':
                    $sql = "SELECT COUNT(location_item.item) FROM location_item RIGHT JOIN location ON location.id = location_item.location AND location_item.type = '$type'";
                    break;
                case 'unlocable':
                    $sql = "SELECT COUNT(item) FROM location_item WHERE location_item.locable = 0 AND location_item.type = '$type'";
                    break;
                case 'not-country':
                    $sql = "SELECT COUNT(location_item.item) FROM location_item RIGHT JOIN location ON location.id = location_item.location AND location_item.type = '$type' AND location.country_code NOT LIKE '{$keyword}'";
                    break;
                case 'country':
                    $sql = "SELECT COUNT(location_item.item) FROM location_item RIGHT JOIN location ON location.id = location_item.location AND location_item.type = '$type' AND location.country_code LIKE '{$keyword}'";
                    break;
                case 'region':
                    $sql = "SELECT COUNT(location_item.item) FROM location_item RIGHT JOIN location ON location.id = location_item.location AND location_item.type = '$type' AND location.region LIKE '{$keyword}'";
                    break;
                case 'geolocation':
                    $sql = "SELECT COUNT(item) FROM location_item WHERE location_item.location = '{$keyword}' AND location_item.type = '$type'";
                    break;
            }

            if (!empty($sql)) {
                $query = self::query($sql);
                $num = $query->fetchColumn();

                return $num;
            } else {
                return false;
            }
        }


    }
}
