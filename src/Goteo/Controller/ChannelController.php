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
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Session;
use Goteo\Application\Message;
use Goteo\Application\View;
use Goteo\Model\Node;
use Goteo\Model\Home;
use Goteo\Model\Project;
use Goteo\Model\Sponsor;
use Goteo\Model\Category;
use Goteo\Library\Text;
use Goteo\Model\Page;
use Goteo\Model\SocialCommitment;
use Goteo\Model\Project\ProjectLocation;


class ChannelController extends \Goteo\Core\Controller {
    public function __construct() {
        $this->dbReplica(true);
        $this->dbCache(true);

        // changing to a responsive theme here
        View::setTheme('responsive');
    }

    // Set the global vars to all the channel views
    private function setChannelContext($id)
    {

        try {
            $channel = Node::get($id);
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e->getMessage());
        }

        //Check if the user can access to the channel
        $user = Session::getUser();

        if(!$channel->active && (!$user || !$channel->userCanView($user)))
            throw new ControllerAccessDeniedException("You don't have permissions to access to this channel!");

        $categories = Category::getNames();

        $side_order = Home::getAll($id, 'side'); // side order

        $types = (!$channel->premium) ? array(
            'popular',
            'recent',
            'success',
            'outdate'
        ): [];

        

        //check if there are elements to show by type
        foreach($types as $key => $type)
        {
            $total = Project::published(['type' => $type], $id, 0, 0, true);
            if(!$total&&$type!='popular')
                unset($types[$key]);
        }

        if (isset($side_order['summary'])) {
            $summary = $channel->getSummary();
        }

        if (isset($side_order['sponsors'])) {
            // Patrocinadores del nodo
            $sponsors = Sponsor::getList($id);
        }

        $this->contextVars([
            'channel'     => $channel,
            'side_order' => $side_order,
            'summary' => $summary,
            'sumcalls' => $sumcalls,
            'sponsors' => $sponsors,
            'categories' => $categories,
            'types' => $types,
            'url_project_create' => '/channel/' . $id . '/create'
        ], 'channel/');
    }

    /**
     * @param  [type] $id   Channel id
     */
    public function indexAction($id, Request $request)
    {

        $this->setChannelContext($id);

        // Proyectos destacados si hay

        $limit = 999;

        if($list = Project::published(['type' => 'promoted'], $id, 0, $limit)) {
            $total = count($list);
        }
        else {
            // if no promotes let's show some random projects...
            $limit = 9;
            $total = $limit;
            $list = Project::published(['type' => 'random'], $id, 0, $limit);
        }

        return $this->viewResponse(
            'channel/list_projects',
            array(
                'projects' => $list,
                'category'=> $category,
                'title_text' => Text::get('node-side-searcher-promote'),
                'type' => $type,
                'total' => $total,
                'limit' => $limit
                )
        );
    }

    /**
     * All channel projects
     * @param  [type] $id   Channel id
     * @param  Request $request [description]
     */
    public function listProjectsAction($id, $type = 'available', $category = null, Request $request)
    {
        $this->setChannelContext($id);

        $limit = 9;
        $status=[3,4,5];
        $filter = ['type' => $type, 'popularity' => 5, 'status' => $status ];

        $title_text = $type === 'available' ? Text::get('regular-see_all') : Text::get('node-side-searcher-'.$type);
        if($category) {
            if($cat = Category::get($category)) {
                $title_text .= ' / '. Text::get('discover-searcher-bycategory-header') . ' ' . $cat->name;
                $filter['category'] = $category;
            }
        }

        $list = Project::published($filter, $id, (int)$request->query->get('pag') * $limit, $limit);
        $total = Project::published($filter, $id, 0, 0, true);

        return $this->viewResponse(
            'channel/list_projects',
            array(
                'projects' => $list,
                'category'=> $category,
                'title_text' => $title_text,
                'type' => $type,
                'total' => $total,
                'limit' => $limit
                )
        );
    }


    /**
     * Initial create project action
     * @param  Request $request [description]
     */
    public function createAction ($id, Request $request)
    {
        // Some context vars for compatibility (to be removed)
        $this->contextVars(['url_project_create' => '/channel/' . $id . '/create']);

        if (!Session::isLogged()) {
            Message::info(Text::get('user-login-required-to_create'));
            return $this->redirect('/user/login?return='.urldecode("/channel/$id/create"));
        }

        if($request->isMethod('post')) {
            // Re-use original post action:
            // The listener ProjectChannelListener does all the work on changing the assigned channel
            return $this->forward('Goteo\Controller\ProjectController::createAction');
        }

        return $this->viewResponse( 'project/create', [
            'social_commitments' => SocialCommitment::getAll(),
            'terms'      => Page::get('howto'),
            'project_defaults' => ['node' => $id]
        ]);
    }

     /**
     * List of channels
     * @param  Request $request [description]
     */
    public function listChannelsAction (Request $request)
    {
        $channels=Node::getAll(['status' => 'active', 'type' => 'channel']);

        foreach ($channels as $chanelId => $channel) {
            if(!$channel->home_img)
                unset($channels[$chanelId]);
        }

        return $this->viewResponse('channels/list', ['channels' => $channels]);
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
    public function discoverProjectsAction($id = null, $filter = '', Request $request)
    {
        try {
            $this->setChannelContext($id);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/');
        }

        $channel = Node::get($id);

        $limit = 12;
        $q = strip_tags($request->query->get('q'));
        $location = strip_tags($request->query->get('location'));
        $latitude = strip_tags($request->query->get('latitude'));
        $longitude = strip_tags($request->query->get('longitude'));
        $category = $request->query->get('category');
        $vars = ['q' => $q, 'category' => $category, 'location' => $location, 'latitude' => $latitude, 'longitude' => $longitude];
        if(Session::isAdmin()) {
            $vars['review'] = $request->query->get('review') === '1' ? 1 : 0 ;
        }

        $filters = $this->getProjectFilters($filter, $vars);
        $filters['node'] = $id;

        $projects = Project::getList($filters, null, 0, $limit);
        $total = Project::getList($filters, null, 0, 0, true);

        return $this->viewResponse('channel/results', [
            'channel' => $channel,
            'projects' => $projects,
            'categories' => Category::getNames(),
            'filter' => $filter,
            'total' => $total,
            'discover_module' => true,
            'limit' => $limit
        ]);

    }

    /**
     * Ajax projects search
     */
    public function ajaxSearchAction($id = null, Request $request) {

        try {
            Node::get($id);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return;
        }

        $limit = $request->get('limit', 24); // extracts from GET, PATH or POST
        $pag = $request->get('pag', 0);
        $limit = max(1, min(25, abs($limit)));
        $pag = max(0, abs($pag));
        $filter = $request->get('filter');
        $q = strip_tags($request->get('q'));
        $location = strip_tags($request->get('location'));
        $latitude = strip_tags($request->get('latitude'));
        $longitude = strip_tags($request->get('longitude'));
        $category = $request->get('category');

        $ofilters = [
            'status' => [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED],
            'published_since' => (new \DateTime('-6 month'))->format('Y-m-d')
        ];
        $filters = $this->getProjectFilters($filter, ['q' => $q, 'category' => $category, 'location' => $location, 'latitude' => $latitude, 'longitude' => $longitude]);
        $filters['node'] = $id;

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
