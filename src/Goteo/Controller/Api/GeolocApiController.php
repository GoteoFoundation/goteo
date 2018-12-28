<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Config;
use Goteo\Application\Session;

use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\InvalidDataException;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;

class GeolocApiController extends AbstractApiController {
    public function __construct() {
        parent::__construct();
        // Activate cache & replica read for this controller
        $this->dbReplica(true);
        $this->dbCache(true);
    }


    /**
     * returns geolocation data from an IP request
     */
    public function geolocationAction(Request $request) {

        $ip = $request->query->has('ip') ? $request->query->get('ip') : $request->getClientIp();

        // Allow only requests from the same host
        $referer = parse_url($request->headers->get('referer'));
        if(strpos($request->headers->get('referer'), $request->getSchemeAndHttpHost()) !== 0) {
            throw new ControllerAccessDeniedException('This API endpoint can only be accessed from the same host');
        }

        $cities = Config::get('geolocation.maxmind.cities');
        // Avoid 500 errors if not configured
        if(!$cities) {
            throw new InvalidDataException('IP cannot be located. Maxmind is not configured');
        }
        // This creates the Reader object, which should be reused across lookups.
        try {
            $reader = new Reader($cities);
            $record = $reader->city($ip);
        } catch(AddressNotFoundException $e) {
            throw new InvalidDataException($e->getMessage());
        }
        // otherwise error 500 will default

        return $this->jsonResponse([
            'city_name'         => $record->city->name,
            'region_name'       => $record->mostSpecificSubdivision->name,
            'country_name'      => $record->country->name,
            'country_code' => $record->country->isoCode,
            'longitude'    => $record->location->longitude,
            'latitude'     => $record->location->latitude,
            'postal_code'    => $record->postal->code,
            'ip_address'     => $record->traits->ipAddress,
        ]);

    }

    /**
     * returns geolocation data from an IP request
     */
    public function geolocateAction(Request $request) {
    }
}
