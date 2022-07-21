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

use DateTime;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Core\Controller;
use Goteo\Model\Category;
use Goteo\Model\Project;
use Goteo\Model\Project\ProjectLocation;
use Symfony\Component\HttpFoundation\Request;

class DiscoverController extends Controller {

    private Project\ProjectFilters $projectFilters;

    public function __construct() {
        $this->dbReplica(true);
        $this->dbCache(true);
        View::setTheme('responsive');
        $this->projectFilters = new Project\ProjectFilters();
    }

    /*
     * Discover projects, general page
     */
    public function searchAction (Request $request, $filter = '') {
        $limit = 12;
        $vars = [];

        $vars = [];

        if ($request->query->has('q'))
            $vars['q'] = strip_tags($request->query->get('q'));

        if ($request->query->has('location') || ( $request->query->has('latitude') && $request->query->has('longitude') )) {
            $vars['location'] = strip_tags($request->query->get('location'));
            $vars['latitude'] = strip_tags($request->query->get('latitude'));
            $vars['longitude'] = strip_tags($request->query->get('longitude'));
        }

        if ($request->query->has('category'))
            $vars['category'] = $request->query->get('category');

        if(Session::isAdmin()) {
            $vars['review'] = $request->query->get('review') === '1' ? 1 : 0 ;
        }

        $filters = $this->projectFilters->getFilters($filter, $vars);

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

    public function ajaxSearchAction(Request $request) {
        $limit = $request->get('limit', 24);
        $pag = $request->get('pag', 0);
        $limit = max(1, min(25, abs($limit)));
        $pag = max(0, abs($pag));
        $filter = $request->get('filter');
        $vars = [];

        if ($request->query->has('q'))
            $vars['q'] = strip_tags($request->query->get('q'));

        if ($request->query->has('location')) {
            $vars['location'] = strip_tags($request->query->get('location'));
            $vars['latitude'] = strip_tags($request->query->get('latitude'));
            $vars['longitude'] = strip_tags($request->query->get('longitude'));
        }

        if ($request->query->has('category'))
            $vars['category'] = $request->query->get('category');

        $ofilters = [
            'status' => [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED],
            'published_since' => (new DateTime('-6 month'))->format('Y-m-d')
        ];
        $filters = $this->projectFilters->getFilters($filter, $vars);

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
