<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Google;

class GoogleGeocoder {

    /**
     * get Address (if found) components from params
     * DOC:
     * https://developers.google.com/maps/documentation/geocoding
     *
     * @param  Array  $params array('address' => '...'), latlng, Check doc for params
     * @return Array|false         Result if success, false otherwise
     */
    public static function getCoordinates(Array $params) {
        $params = $params + array('sensor' => 'false');

        $url = 'http://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params);
        // echo "URL: $url\n";
        $result = json_decode(file_get_contents($url), true);
        $data = array();
        if($result['status'] == 'OK') {
            // print_r($result['results'][0]);
            $data['latitude'] = $result['results'][0]['geometry']['location']['lat'];
            $data['longitude'] = $result['results'][0]['geometry']['location']['lng'];
            $data['city'] = '';
            $data['country'] = '';
            $data['country_code'] = '';
            $data['region'] = '';
            foreach($result['results'][0]['address_components'] as $ob) {
                if($ob['types'][0] === 'country' && $ob['types'][1] === 'political') {
                    $data['country'] = $ob['long_name'];
                    $data['country_code'] = $ob['short_name'];
                }
                if($ob['types'][0] === 'locality' && $ob['types'][1] === 'political') {
                    $data['city'] = $ob['long_name'];
                }
                if(($ob['types'][0] === 'administrative_area_level_1' || $ob['types'][0] === 'administrative_area_level_2') && $ob['types'][1] === 'political') {
                    $data['region'] = $ob['long_name'];
                }
            }
            return $data;
        }
        return false;
    }
}
