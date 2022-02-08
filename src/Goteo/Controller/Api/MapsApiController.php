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

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\View;
use Goteo\Model\Image;
use Goteo\Model\Node;
use Goteo\Model\Project;
use Goteo\Model\Matcher;
use Goteo\Model\Project\ProjectLocation;


class MapsApiController extends AbstractApiController {

    public function __construct() {
        parent::__construct();
        $this->dbReplica(true);
        $this->dbCache(true);
        View::setTheme('responsive');
    }

    public function channelAction($cid = null)
    {
        if ($cid) {
            try {
              $channel = Node::get($cid);
            } catch (ModelNotFoundException $e) {
              Message::error($e->getMessage());
            }

            $list_projects = [];
            $conf = $channel->getConfig();

            if ($conf['projects']) {
              $total = Project::getList($conf['projects'], $cid, 0, 0, true);
              $projects = Project::getList($conf['projects'], $cid, 0, $total);
            } else {
              $total = Project::getList(['node' => $channel->id], $cid, 0, 0, true);
              $projects = Project::getList(['node' => $channel->id], $cid, 0, $total);
            }

            foreach($projects as $project) {
                $ob = ['id' => $project->id,
                   'name' => $project->name,
                   'amount' => $project->amount,
                   'invested' => $project->invested,
                   'num_investors' => $project->num_investors,
                   'image' => Image::get($project->image)->getLink(120,120),
                   'project_location' => ProjectLocation::get($project->id),
                   'popup' => View::render('map/partials/project_popup.php', array('project' => $project))];
                $list_projects[] = $ob;
            }

            $workshops = $channel->getAllWorkshops();
            $list_workshops = array_map(function($workshop) {
              $ob = [
                'id' => $workshop->id,
                'title' => $workshop->title,
                'subtitle' => $workshop->subtitle,
                'workshop_location' => $workshop->getLocation(),
                'image' => Image::get($workshop->header_image)->getLink(120,120),
                'popup' => View::render('map/partials/workshop_popup.php', array('workshop' => $workshop))
              ];
              return $ob;
            }, $workshops);
        }

        return $this->jsonResponse([
            'projects' => $list_projects,
            'workshops' => $list_workshops,
        ]);
    }

    public function matcherAction($mid = null) {

        $list_projects = [];

        if ($mid) {
            try {
                $matcher = Matcher::get($mid);
                $projects = $matcher->getListProjects('active');
            } catch (ModelNotFoundException $e) {
                Message::error($e->getMessage());
            }

            try {
                $channel = Node::get($mid);
                $conf = $channel->getConfig();

                if ($conf['projects']) {
                  $total = Project::getList($conf['projects'], $mid, 0, 0, true);
                  $projects = Project::getList($conf['projects'], $mid, 0, $total);
                } else {
                  $total = Project::getList(['node' => $channel->id], $mid, 0, 0, true);
                  $projects = Project::getList(['node' => $channel->id], $mid, 0, $total);
                }
            } catch (ModelNotFoundException $e) {}

            foreach($projects as $project) {
                $ob = ['id' => $project->id,
                   'name' => $project->name,
                   'amount' => $project->amount,
                   'invested' => $project->invested,
                   'num_investors' => $project->num_investors,
                   'image' => Image::get($project->image)->getLink(120,120),
                   'project_location' => ProjectLocation::get($project->id),
                   'popup' => View::render('map/partials/project_popup.php', array('project' => $project))];
                $list_projects[] = $ob;
            }
        }

        return $this->jsonResponse([
            'projects' => $list_projects
        ]);
    }
}
