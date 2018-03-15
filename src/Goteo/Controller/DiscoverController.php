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

use Symfony\Component\HttpFoundation\Request;

use Goteo\Application\View;
use Goteo\Application\Session;
use Goteo\Model\Category;
use Goteo\Model\Project;
use Goteo\Model\Project\ProjectLocation;

class DiscoverController extends \Goteo\Core\Controller {

    public function __construct() {
        // Cache & replica read activated in this controller
        \Goteo\Core\DB::cache(true);
        // \Goteo\Core\DB::replica(true);
        View::setTheme('responsive');
    }

    /**
    * Returns an array suitable for Project::getList($filters)
     */
    protected function getProjectFilters($filter, $vars = []) {
        $filters = $ofilters = [
            'status' => [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED],
            'published_since' => (new \DateTime('-6 month'))->format('Y-m-d')
        ];

        $filters['order'] = 'project.status ASC, project.published DESC, project.name ASC';
        if($vars['q']) {
            $filters['global'] = $vars['q'];
            unset($filters['published_since']);
            $filters['status'] = [ Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED ];
        }
        elseif($vars['category']) {
            $filters['category'] = $vars['category'];
            unset($filters['published_since']);
            $filters['status'] = [ Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED ];
        }
        elseif($vars['location'] || ($vars['latitude'] && $vars['longitude'])) {
            // $filters['location'] = $vars['location'];
            unset($filters['published_since']);
            $filters['location'] = new ProjectLocation([ 'location' => $vars['location'], 'latitude' => $vars['latitude'], 'longitude' => $vars['longitude'], 'radius' => 300 ]);
            $filters['status'] = [ Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED ];
            $filters['order'] = 'Distance ASC, project.status ASC, project.published DESC, project.name ASC';
        }
        elseif($filter === 'near') {
            // Nearby defined as 300Km distance
            // Any LocationInterface will do (UserLocation, ProjectLocation, ...)
            $filters['location'] = new ProjectLocation([ 'latitude' => $vars['latitude'], 'longitude' => $vars['longitude'], 'radius' => 300 ]);
            $filters['order'] = 'Distance ASC, project.status ASC, project.published DESC, project.name ASC';
        } elseif($filter === 'outdated') {
            $filters['type'] = 'outdated';
            $filters['status'] = Project::STATUS_IN_CAMPAIGN;
            $filters['order'] = 'project.days ASC, project.published DESC, project.name ASC';
        } elseif($filter === 'promoted') {
            $filters['type'] = 'promoted';
            $filters['status'] = Project::STATUS_IN_CAMPAIGN;
            $filters['order'] = 'promote.order ASC, project.published DESC, project.name ASC';
        } elseif($filter === 'popular') {
            $filters['type'] = 'popular';
            $filters['status'] = Project::STATUS_IN_CAMPAIGN;
            $filters['order'] = 'project.popularity DESC, project.published DESC, project.name ASC';
        } elseif($filter === 'succeeded') {
            $filters['type'] = 'succeeded';
            $filters['status'] = [Project::STATUS_FUNDED, Project::STATUS_FULFILLED];
            $filters['order'] = 'project.published DESC, project.name ASC';
            // $filters['published_since'] = (new \DateTime('-12 month'))->format('Y-m-d');
            unset($filters['published_since']);
        } elseif($filter === 'fulfilled') {
            $filters['status'] = [Project::STATUS_FULFILLED];
            $filters['order'] = 'project.published DESC, project.name ASC';
            // $filters['published_since'] = (new \DateTime('-24 month'))->format('Y-m-d');
            unset($filters['published_since']);
        } elseif($filter === 'archived') {
            $filters['status'] = [Project::STATUS_UNFUNDED];
            $filters['order'] = 'project.published DESC, project.name ASC';
            $filters['published_since'] = (new \DateTime('-24 month'))->format('Y-m-d');
        } elseif($filter === 'matchfunding') {
            $filters['type'] = 'matchfunding';
            // $filters['published_since'] = (new \DateTime('-24 month'))->format('Y-m-d');
            unset($filters['published_since']);
        } elseif($filter === 'recent') {
            $filters['type'] = 'recent';
        }

        if($vars['review']) {
            $filters['status'] = [ Project::STATUS_EDITING, Project::STATUS_REVIEWING, Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED ];
            $filters['is_draft'] = true;
            // unset($filters['published_since']);
        }
        return $filters;
    }

    /*
     * Discover projects, general page
     */
    public function searchAction ($filter = '', Request $request) {
        if(empty($type)) $type = 'promoted';

        $limit = 12;
        $q = $request->query->get('q');
        $location = $request->query->get('location');
        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');
        $category = $request->query->get('category');
        $vars = ['q' => $q, 'category' => $category, 'location' => $location, 'latitude' => $latitude, 'longitude' => $longitude];
        if(Session::isAdmin()) {
            $vars['review'] = $request->query->get('review') !== '0';
        }

        $filters = $this->getProjectFilters($filter, $vars);

        $projects = Project::getList($filters, null, 0, $limit);
        $total = Project::getList($filters, null, 0, 0, true);

        return $this->viewResponse('discover/results', [
            'projects' => $projects,
            'categories' => Category::getNames(),
            'filter' => $filter,
            'total' => $total,
            'limit' => $limit
        ]);

    }

    /**
     * Ajax projects search
     */
    public function ajaxSearchAction(Request $request) {

        $limit = $request->get('limit', 24); // extracts from GET, PATH or POST
        $pag = $request->get('pag', 0);
        $limit = max(1, min(25, abs($limit)));
        $pag = max(0, abs($pag));
        $filter = $request->get('filter');
        $q = $request->get('q');
        $location = $request->get('location');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $category = $request->get('category');

        $ofilters = [
            'status' => [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED],
            'published_since' => (new \DateTime('-6 month'))->format('Y-m-d')
        ];
        $filters = $this->getProjectFilters($filter, ['q' => $q, 'category' => $category, 'location' => $location, 'latitude' => $latitude, 'longitude' => $longitude]);

        $offset = $pag * $limit;
        $total_projects = 0;
        $projects = Project::getList($filters, null, $offset, $limit);
        if($projects) {
            $total_projects = Project::getList($filters, null, 0, 0, true);
        } elseif(!$request->query->has('strict')) {
            // Home controller does not send 'strict' query string, we always want projects in home:
            $projects = Project::getList($ofilters, null, $offset, $limit);
            $total_projects = Project::getList($ofilters, null, 0, 0, true);
        }

        $vars = [
            'filter' => $filter,
            'limit' => $limit,
            'pag' => $pag,
            'total' => $total_projects,
            'items' => []
        ];
        foreach($projects as $p) {
            $vars['items'][] = View::render('project/widgets/normal', ['project' => $p]);
        }
        return $this->jsonResponse($vars);
    }

}

