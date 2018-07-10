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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Goteo\Application\Exception\ControllerAccessDeniedException;

use Goteo\Application\Session;
use Goteo\Application\Message;
use Goteo\Application\View;
use Goteo\Model\Node;
use Goteo\Model\Home;
use Goteo\Model\Project;
use Goteo\Model\Sponsor;
// convocatorias en portada
use Goteo\Model\Call;
use Goteo\Model\User;
use Goteo\Model\Category;
use Goteo\Library\Text;
use Goteo\Model\Page;
use Goteo\Model\Project\Conf;
use Goteo\Model\SocialCommitment;
use Goteo\Console\UsersSend;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterProjectEvent;



class ChannelController extends \Goteo\Core\Controller {
    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
    }

    // Set the global vars to all the channel views
    private function setChannelContext($id)
    {

        $channel = Node::get($id);

        //Check if the user can access to the channel
        $user = Session::getUser();

        if(!$channel->active && (!$user || !$channel->userCanView($user)))
            throw new ControllerAccessDeniedException("You don't have permissions to access to this channel!");

        $categories = Category::getNames();

        $side_order = Home::getAll($id, 'side'); // side order

        $types = array(
            'popular',
            'recent',
            'success',
            'outdate'
        );

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

}
