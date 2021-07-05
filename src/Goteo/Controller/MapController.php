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

use Goteo\Util\Map\MapOSM;
use Goteo\Application\View;
use Goteo\Application\Exception;

use Goteo\Model\Node;
use Goteo\Model\Matcher;
use Goteo\Model\Project;
use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\Call;
use Goteo\Model\Workshop;
use Goteo\Model\Contract\BaseDocument;

use Symfony\Component\HttpFoundation\Request;

class MapController extends \Goteo\Core\Controller {

  public function __construct() {
    $this->dbReplica(true);
    $this->dbCache(true);
    View::setTheme('responsive');
  }

	public function mapAction(Request $request) {

    $cid = strip_tags($request->get('channel'));
    $mid = strip_tags($request->get('matcher'));
    $geojson = strip_tags($request->get('geojson'));

    $map = new MapOSM('100%');

    try {
      $geojson_document = BaseDocument::getByName($geojson);
      $map->setGeoJSON($geojson_document->getLink());
    } catch(\Exception $e) {}

    if ($cid) {
      try {
        $channel = Node::get($cid);
      } catch (ModelNotFoundException $e) {
        Message::error($e->getMessage());
      }
      $map->setChannel($cid);
    }

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

  public function exactMapAction($zoom, $latlng, Request $request) {

    $cid = strip_tags($request->get('channel'));
    $mid = strip_tags($request->get('matcher'));
    $geojson = strip_tags($request->get('geojson'));

    $map = new MapOSM('100%');
    $map->setZoom($zoom);
    $map->setCenter(explode(',',$latlng));

    try {
      $geojson_document = BaseDocument::getByName($geojson);
      $map->setGeoJSON($geojson_document->getLink());
    } catch(\Exception $e) {}

    if ($cid) {
      try {
        $channel = Node::get($cid);
      } catch (ModelNotFoundException $e) {
        Message::error($e->getMessage());
      }
      $map->setChannel($cid);
    }

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
