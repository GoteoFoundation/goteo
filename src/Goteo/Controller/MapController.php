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

use Goteo\Model\Node;
use Goteo\Model\Project;
use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\Call;
use Goteo\Model\Workshop;

use Symfony\Component\HttpFoundation\Request;

class MapController extends \Goteo\Core\Controller {

  public function __construct() {
    $this->dbReplica(true);
    $this->dbCache(true);
    View::setTheme('responsive');
  }

	public function channelAction($cid = null, Request $request) {

    $height = strip_tags($request->get('height'));
		try {
			$channel = Node::get($cid);
		} catch (ModelNotFoundException $e) {
			Message::error($e->getMessage());
    }

    $projects = Project::getList(['node' => $channel->id]);
    foreach($projects as $project) {
      $project->project_location = ProjectLocation::get($project->id);
    }
    
    $workshops = $channel->getAllWorkshops();
    foreach($workshops as $workshop) {
      $workshop->workshop_location = $workshop->getLocation();
    }

    $map = new MapOSM(($height)? $height : 500);
    $map->setProjects($projects);
    $map->setWorkshops($workshops);
    
    return $this->viewResponse('map/map_canvas', ['map'  => $map]);
  }
}
