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

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Goteo\Application\Config;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ControllerException;
use Goteo\Application\Exception\InvalidDataException;
use Goteo\Model\Location\LocationItem;
use Symfony\Component\HttpFoundation\Request;

class GeolocApiController extends AbstractApiController {
    public function __construct() {
        parent::__construct();
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
    public function geolocateAction(Request $request, $type, $id='') {
        if(!$this->user) {
            throw new ControllerAccessDeniedException();
        }
        if(!$type) {
            throw new ControllerException('Type required');
        }
        $result = [];
        $values = [
            'city' => $request->request->get('city'),
            'region' => $request->request->get('region'),
            'country' => $request->request->get('country'),
            'country_code' => $request->request->get('country_code'),
            'longitude' => $request->request->get('longitude'),
            'latitude' => $request->request->get('latitude'),
            'method' => $request->request->get('method')
        ];
        foreach(['radius', 'locable', 'info'] as $key) {
            if($request->request->has($key))
                $values[$key] = $request->request->get($key);
        }

        if($type === 'user' && empty($id)) $id = $this->user->id;
        if(!$id) throw new ControllerException('Id required');
        $values['id'] = $id;

        $instance = LocationItem::create($type, $values);

        if(!$instance->userCanView($this->user)) {
            throw new ControllerAccessDeniedException("User [{$this->user->id}] cannot view location for type [$type:$id]");
        }

        if($request->isMethod('post')) {
            if(!$instance->userCanEdit($this->user)) {
                throw new ControllerAccessDeniedException("User [{$this->user->id}] cannot edit location for type [$type:$id]");
            }

            $errors = [];
            // save the whole instance if latitude & longitude present
            if($request->request->has('latitude') && $request->request->has('longitude')) {
                if ($instance->save($errors)) {
                    $result['msg'] = "Location successfully added for [$type]";
                    $result['location'] = $instance;
                } else {
                    throw new InvalidDataException('Localization saving errors: '. implode(',', $errors));
                }
            } else {
                //Just changes some properties (locable, info)
                foreach($request->request->all() as $key => $value) {
                    if($key === 'locable' || $key === 'info') {
                        if($instance::setProperty($id, $key, $value, $errors)) {
                            $result['msg'] = "Property succesfully changed for [$type]";
                        }
                        else {
                            throw new InvalidDataException('Localization update errors: '. implode(',', $errors));
                        }
                    }
                }
            }
        } else {
            $instance = $instance::get($instance->id);
        }

        $result['type'] = $type;
        $result['location'] = $instance;
        $result['item'] = LocationItem::getType($type);
        $result['class'] = LocationItem::getModelClass($type);

        return $this->jsonResponse($result);
    }
}
