<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Exception;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Message;
use Goteo\Application\View;
use Goteo\Core\Controller;
use Goteo\Model\Contract\BaseDocument;
use Goteo\Model\Matcher;
use Goteo\Model\Node;
use Goteo\Util\Map\MapOSM;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MapController extends Controller {

    public function __construct() {
        $this->dbReplica(true);
        $this->dbCache(true);
        View::setTheme('responsive');
    }

	public function mapAction(Request $request): Response {
        $map = new MapOSM('100%');

        $geojson = strip_tags($request->query->get('geojson'));
        try {
            $geojson_document = BaseDocument::getByName($geojson);
            $map->setGeoJSON($geojson_document->getLink());
        } catch(Exception $e) {}

        $cid = strip_tags($request->query->getAlnum('channel'));
        if ($cid) {
            try {
                $channel = Node::get($cid);
            } catch (ModelNotFoundException $e) {
                Message::error($e->getMessage());
            }
            $map->setChannel($cid);
        }

        $mid = strip_tags($request->query->getAlnum('matcher'));
        if ($mid) {
            try {
                $matcher = Matcher::get($mid);
                $map->setMatcher($mid);
            } catch( ModelNotFoundException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('map/map_canvas', ['map'  => $map]);
  }

    public function exactMapAction($zoom, $latlng, Request $request): Response {

        $map = new MapOSM('100%');
        $map->setZoom($zoom);
        $map->setCenter(explode(',',$latlng));

        $geojson = strip_tags($request->query->get('geojson'));
        try {
            $geojson_document = BaseDocument::getByName($geojson);
            $map->setGeoJSON($geojson_document->getLink());
        } catch(Exception $e) {}

        $cid = strip_tags($request->query->getAlnum('channel'));
        if ($cid) {
            try {
                $channel = Node::get($cid);
            } catch (ModelNotFoundException $e) {
                Message::error($e->getMessage());
            }
            $map->setChannel($cid);
        }

        $mid = strip_tags($request->query->getAlnum('matcher'));
        if ($mid) {
            try {
                $matcher = Matcher::get($mid);
                $map->setMatcher($mid);
            } catch( ModelNotFoundException $e) {
                Message::error($e->getMessage());
            }
        }

        return $this->viewResponse('map/map_canvas', ['map'  => $map]);
  }

}
