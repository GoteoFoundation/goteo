<?php

namespace Goteo\Library {

	/*
	 * Clase para cosas de localizaciones
	 * Para sacar las existentes, las coincidencias proyecto-usuario, las de gmaps, y geo-localizacion
	 */
    class Geoloc {

        public static $countries = array (
            "AD"=>"Andorra", 
            "AE"=>"United Arab Emirates", 
            "AF"=>"Afghanistan", 
            "AG"=>"Antigua and Barbuda", 
            "AI"=>"Anguilla", 
            "AL"=>"Albania", 
            "AM"=>"Armenia", 
            "AO"=>"Angola", 
            "AQ"=>"Antarctica", 
            "AR"=>"Argentina", 
            "AS"=>"American Samoa", 
            "AT"=>"Austria", 
            "AU"=>"Australia", 
            "AW"=>"Aruba", 
            "AX"=>"Åland Islands", 
            "AZ"=>"Azerbaijan", 
            "BA"=>"Bosnia and Herzegovina", 
            "BB"=>"Barbados", 
            "BD"=>"Bangladesh", 
            "BE"=>"Belgium", 
            "BF"=>"Burkina Faso", 
            "BG"=>"Bulgaria", 
            "BH"=>"Bahrain", 
            "BI"=>"Burundi", 
            "BJ"=>"Benin", 
            "BL"=>"Saint Barthélemy", 
            "BM"=>"Bermuda", 
            "BN"=>"Brunei Darussalam", 
            "BO"=>"Bolivia, Plurinational State of", 
            "BQ"=>"Bonaire, Sint Eustatius and Saba", 
            "BR"=>"Brazil", 
            "BS"=>"Bahamas", 
            "BT"=>"Bhutan", 
            "BV"=>"Bouvet Island", 
            "BW"=>"Botswana", 
            "BY"=>"Belarus", 
            "BZ"=>"Belize", 
            "CA"=>"Canada", 
            "CC"=>"Cocos (Keeling) Islands", 
            "CD"=>"Congo, the Democratic Republic of the", 
            "CF"=>"Central African Republic", 
            "CG"=>"Congo", 
            "CH"=>"Switzerland", 
            "CI"=>"Côte d'Ivoire", 
            "CK"=>"Cook Islands", 
            "CL"=>"Chile", 
            "CM"=>"Cameroon", 
            "CN"=>"China", 
            "CO"=>"Colombia", 
            "CR"=>"Costa Rica", 
            "CU"=>"Cuba", 
            "CV"=>"Cape Verde", 
            "CW"=>"Curaçao", 
            "CX"=>"Christmas Island", 
            "CY"=>"Cyprus", 
            "CZ"=>"Czech Republic", 
            "DE"=>"Germany", 
            "DJ"=>"Djibouti", 
            "DK"=>"Denmark", 
            "DM"=>"Dominica", 
            "DO"=>"Dominican Republic", 
            "DZ"=>"Algeria", 
            "EC"=>"Ecuador", 
            "EE"=>"Estonia", 
            "EG"=>"Egypt", 
            "EH"=>"Western Sahara", 
            "ER"=>"Eritrea", 
            "ES"=>"Spain", 
            "ET"=>"Ethiopia", 
            "FI"=>"Finland", 
            "FJ"=>"Fiji", 
            "FK"=>"Falkland Islands (Malvinas)", 
            "FM"=>"Micronesia, Federated States of", 
            "FO"=>"Faroe Islands", 
            "FR"=>"France", 
            "GA"=>"Gabon", 
            "GB"=>"United Kingdom", 
            "GD"=>"Grenada", 
            "GE"=>"Georgia", 
            "GF"=>"French Guiana", 
            "GG"=>"Guernsey", 
            "GH"=>"Ghana", 
            "GI"=>"Gibraltar", 
            "GL"=>"Greenland", 
            "GM"=>"Gambia", 
            "GN"=>"Guinea", 
            "GP"=>"Guadeloupe", 
            "GQ"=>"Equatorial Guinea", 
            "GR"=>"Greece", 
            "GS"=>"South Georgia and the South Sandwich Islands", 
            "GT"=>"Guatemala", 
            "GU"=>"Guam", 
            "GW"=>"Guinea-Bissau", 
            "GY"=>"Guyana", 
            "HK"=>"Hong Kong", 
            "HM"=>"Heard Island and McDonald Islands", 
            "HN"=>"Honduras", 
            "HR"=>"Croatia", 
            "HT"=>"Haiti", 
            "HU"=>"Hungary", 
            "ID"=>"Indonesia", 
            "IE"=>"Ireland", 
            "IL"=>"Israel", 
            "IM"=>"Isle of Man", 
            "IN"=>"India", 
            "IO"=>"British Indian Ocean Territory", 
            "IQ"=>"Iraq", 
            "IR"=>"Iran, Islamic Republic of", 
            "IS"=>"Iceland", 
            "IT"=>"Italy", 
            "JE"=>"Jersey", 
            "JM"=>"Jamaica", 
            "JO"=>"Jordan", 
            "JP"=>"Japan", 
            "KE"=>"Kenya", 
            "KG"=>"Kyrgyzstan", 
            "KH"=>"Cambodia", 
            "KI"=>"Kiribati", 
            "KM"=>"Comoros", 
            "KN"=>"Saint Kitts and Nevis", 
            "KP"=>"Korea, Democratic People's Republic of", 
            "KR"=>"Korea, Republic of", 
            "KW"=>"Kuwait", 
            "KY"=>"Cayman Islands", 
            "KZ"=>"Kazakhstan", 
            "LA"=>"Lao People's Democratic Republic", 
            "LB"=>"Lebanon", 
            "LC"=>"Saint Lucia", 
            "LI"=>"Liechtenstein", 
            "LK"=>"Sri Lanka", 
            "LR"=>"Liberia", 
            "LS"=>"Lesotho", 
            "LT"=>"Lithuania", 
            "LU"=>"Luxembourg", 
            "LV"=>"Latvia", 
            "LY"=>"Libya", 
            "MA"=>"Morocco", 
            "MC"=>"Monaco", 
            "MD"=>"Moldova, Republic of", 
            "ME"=>"Montenegro", 
            "MF"=>"Saint Martin (French part)", 
            "MG"=>"Madagascar", 
            "MH"=>"Marshall Islands", 
            "MK"=>"Macedonia, the former Yugoslav Republic of", 
            "ML"=>"Mali", 
            "MM"=>"Myanmar", 
            "MN"=>"Mongolia", 
            "MO"=>"Macao", 
            "MP"=>"Northern Mariana Islands", 
            "MQ"=>"Martinique", 
            "MR"=>"Mauritania", 
            "MS"=>"Montserrat", 
            "MT"=>"Malta", 
            "MU"=>"Mauritius", 
            "MV"=>"Maldives", 
            "MW"=>"Malawi", 
            "MX"=>"Mexico", 
            "MY"=>"Malaysia", 
            "MZ"=>"Mozambique", 
            "NA"=>"Namibia", 
            "NC"=>"New Caledonia", 
            "NE"=>"Niger", 
            "NF"=>"Norfolk Island", 
            "NG"=>"Nigeria", 
            "NI"=>"Nicaragua", 
            "NL"=>"Netherlands", 
            "NO"=>"Norway", 
            "NP"=>"Nepal", 
            "NR"=>"Nauru", 
            "NU"=>"Niue", 
            "NZ"=>"New Zealand", 
            "OM"=>"Oman", 
            "PA"=>"Panama", 
            "PE"=>"Peru", 
            "PF"=>"French Polynesia", 
            "PG"=>"Papua New Guinea", 
            "PH"=>"Philippines", 
            "PK"=>"Pakistan", 
            "PL"=>"Poland", 
            "PM"=>"Saint Pierre and Miquelon", 
            "PN"=>"Pitcairn", 
            "PR"=>"Puerto Rico", 
            "PS"=>"Palestine, State of", 
            "PT"=>"Portugal", 
            "PW"=>"Palau", 
            "PY"=>"Paraguay", 
            "QA"=>"Qatar", 
            "RE"=>"Réunion", 
            "RO"=>"Romania", 
            "RS"=>"Serbia", 
            "RU"=>"Russian Federation", 
            "RW"=>"Rwanda", 
            "SA"=>"Saudi Arabia", 
            "SB"=>"Solomon Islands", 
            "SC"=>"Seychelles", 
            "SD"=>"Sudan", 
            "SE"=>"Sweden", 
            "SG"=>"Singapore", 
            "SH"=>"Saint Helena, Ascension and Tristan da Cunha", 
            "SI"=>"Slovenia", 
            "SJ"=>"Svalbard and Jan Mayen", 
            "SK"=>"Slovakia", 
            "SL"=>"Sierra Leone", 
            "SM"=>"San Marino", 
            "SN"=>"Senegal", 
            "SO"=>"Somalia", 
            "SR"=>"Suriname", 
            "SS"=>"South Sudan", 
            "ST"=>"Sao Tome and Principe", 
            "SV"=>"El Salvador", 
            "SX"=>"Sint Maarten (Dutch part)", 
            "SY"=>"Syrian Arab Republic", 
            "SZ"=>"Swaziland", 
            "TC"=>"Turks and Caicos Islands", 
            "TD"=>"Chad", 
            "TF"=>"French Southern Territories", 
            "TG"=>"Togo", 
            "TH"=>"Thailand", 
            "TJ"=>"Tajikistan", 
            "TK"=>"Tokelau", 
            "TL"=>"Timor-Leste", 
            "TM"=>"Turkmenistan", 
            "TN"=>"Tunisia", 
            "TO"=>"Tonga", 
            "TR"=>"Turkey", 
            "TT"=>"Trinidad and Tobago", 
            "TV"=>"Tuvalu", 
            "TW"=>"Taiwan, Province of China", 
            "TZ"=>"Tanzania, United Republic of", 
            "UA"=>"Ukraine", 
            "UG"=>"Uganda", 
            "UM"=>"United States Minor Outlying Islands", 
            "US"=>"United States", 
            "UY"=>"Uruguay", 
            "UZ"=>"Uzbekistan", 
            "VA"=>"Holy See (Vatican City State)", 
            "VC"=>"Saint Vincent and the Grenadines", 
            "VE"=>"Venezuela, Bolivarian Republic of", 
            "VG"=>"Virgin Islands, British", 
            "VI"=>"Virgin Islands, U.S.", 
            "VN"=>"Viet Nam", 
            "VU"=>"Vanuatu", 
            "WF"=>"Wallis and Futuna", 
            "WS"=>"Samoa", 
            "YE"=>"Yemen", 
            "YT"=>"Mayotte", 
            "ZA"=>"South Africa", 
            "ZM"=>"Zambia", 
            "ZW"=>"Zimbabwe"
        );
        
        private static $gmaps_api_statuses = array(
            "OK" => 'No errors occurred; the address was successfully parsed and at least one geocode was returned.',
            "ZERO_RESULTS" => 'The geocode was successful but returned no results. This may occur if the geocode was passed a non-existent address or a latlng in a remote location.',
            "OVER_QUERY_LIMIT" => 'You are over your quota.',
            "REQUEST_DENIED" => 'Your request was denied, generally because of lack of a sensor parameter.',
            "INVALID_REQUEST" => 'Generally, the query (address or latlng) is missing.',
            "UNKNOWN_ERROR" => 'The request could not be processed due to a server error. The request may succeed if you try again.'
        );
        
        /**
         * Obtiene los datos de la dirección IP
         * @param mixed $input array de datos
         */
        public static function searchLoc($input = array()) {
            
            $result = array();
            
//            echo 'Recibe:<br />';
//            echo \trace($input);
            if (!empty($input['latlng'])) {
                // peticion a gmaps API
                $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng={$input['latlng']}&sensor=false&language=es";
//                echo 'URL: '.$url.'<br />';
                $meta = file_get_contents($url);
                $data = json_decode($meta);
                
            } if (!empty($input['address'])) {
                $url = "http://maps.googleapis.com/maps/api/geocode/json?address=".  urlencode($input['address'])."&sensor=false&language=es";
//                echo 'URL: '.$url.'<br />';
                $meta = file_get_contents($url);
                $data = json_decode($meta);
            }
            
            // checkeo el status
            if ($data->status != 'OK') {
/*                
                echo "Error {$data->status} ".self::$gmaps_api_statuses[$data->status]."<br />
                    URL: {$url}<br />
                    RESPONSE:<br />" . print_r($data, 1)."<br />";
*/                
                @mail('gmaps_api_fail@doukehsi.org', 'Error en la petición a la api gmaps en ' . SITE_URL, "Error {$data->status} ".self::$gmaps_api_statuses[$data->status]."
                    URL: {$url}
                    INPUT:
                    " . print_r($input, 1)."
                    RESPONSE:
                    " . print_r($data, 1));
                    
            } else {
                $_SESSION['last_gmaps_response_data'] = $data;
                
                // OK
                foreach($data->results as $res) {

                    // PAso de todo lo que sea rooftop o poligonichungo
                    if ($res->geometry->location_type != "APPROXIMATE") continue;
                    
//                    echo "Formated: {$res->formatted_address}<br />";
                    
                    foreach($res->address_components as $comp) {

                        // segun el tipo lo guardo 
                        foreach ($comp->types as $type) {
                            if ($type == 'locality') {
//                                echo \trace($comp)."<br />";
                                $result['location'] = $comp->long_name;
                                continue;
                            } elseif ($type == 'country') {
//                                echo \trace($comp)."<br />";
                                $result['country'] = $comp->long_name;
                                continue;
                            } elseif (substr($type, 0, strlen('administrative_area')) == 'administrative_area') {
//                                echo \trace($comp)."<br />";
                                if (empty($result['location'])) {
                                    $result['location'] = $comp->long_name;
                                } else {
                                    $result['region'] = $comp->long_name;
                                }
                                continue;
                            }
                        }
                        
                        if (!empty($result['location']) && !empty($result['region']) && !empty($result['country'])) break;

                    }

                    if (!isset($input['latlng'])) {
                        $result['lat'] = $res->geometry->location->lat;
                        $result['lon'] = $res->geometry->location->lng;
                    }

                    if (!empty($result['location']) && !empty($result['region']) && !empty($result['country'])) break;
                }

                return $result;
            }
            
            return null;
        }        
        
        /**
         * Obtiene los datos de la dirección IP
         * @param string $ip Dirección IP a buscar
         */
        public static function getIpData($ip) {
//            die(file_get_contents('http://freegeoip.appspot.com/json/'.$ip));
          //$meta = json_decode();
//          $meta = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));

//          $meta = simplexml_load_string(file_get_contents('http://whatismyipaddress.com/ip/'.$ip));
          
            $url = 'http://api.hostip.info/?ip='.$ip;
            $xmlData = static::file_get_contents_curl($url);
            $meta = static::parseLocationData($xmlData);
            return $meta;
            
            /*
            $ipinfodb_key = ''; 
            $url = 'http://api.ipinfodb.com/v3/ip-city/?key='.$ipinfodb_key.'&ip='.$ip.'&format=xml';
			$xmlData = static::file_get_contents_curl($url);
            //$xml = @file_get_contents($url);

			try{
				$response = @new SimpleXMLElement($xmlData);

				foreach($response as $field=>$value){
					$result[(string)$field] = (string)$value;
				}

				return $result;
			}
			catch(Exception $e){
				die($e->getMessage());
				return null;
			}
            */
            
        }
     
        
        
	private static function parseLocationData($xmlData)
	{
		// Use of Simple XML extension of PHP 5
		$xml = simplexml_load_string($xmlData);

		if (!is_object($xml))
		    throw new Exception('Error reading XML');
		
		$infoHost = $xml->xpath('//gml:featureMember');
		$city = $xml->xpath('//gml:featureMember//gml:name');

		$coordinates = $infoHost[0]->xpath('//gml:coordinates');
		$coordinates = split(',', (string) $coordinates[0]);				
		
		$info = array (
			"City"  			=> (string) $city[0],
			"CountryName" 		=> (string)	$infoHost[0]->Hostip->countryName,
			"CountryCode" => (string)	$infoHost[0]->Hostip->countryAbbrev,
			"Longitude"			=> $coordinates[0],
			"Latitude"			=> $coordinates[1]
		);
		
		return $info;
	}

	private static function file_get_contents_curl($url) 
	{
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
	
		$data = curl_exec($ch);
		curl_close($ch);
	
		return $data;
	}
        
	}

}