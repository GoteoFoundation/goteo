<?php

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Request;

use Goteo\Application\Session;
use Goteo\Application;
use Goteo\Application\View;
use Goteo\Model;
use Goteo\Model\Node;
use Goteo\Model\Home;
use Goteo\Model\Project;
use Goteo\Model\Banner;
use Goteo\Model\Stories;
use Goteo\Model\News;
use Goteo\Model\Post;
use // esto son entradas en portada o en footer
    Goteo\Model\Promote;
use Goteo\Model\Patron;
use Goteo\Model\Campaign;
use // convocatorias en portada
    Goteo\Model\Call,
    Goteo\Model\User;
use Goteo\Model\Icon;
use Goteo\Model\Category;
use Goteo\Library\Text;
use Goteo\Library\Page;

class ChannelController extends \Goteo\Core\Controller {

    // Set the global vars to all the channel views
    private function setChannelContext($id)
    {
        $channel = Node::get($id);

        $categories=Category::getList();

        $side_order = Home::getAllSide($id); //orden de lateral side

        $types = array(
            'popular',
            'recent',
            'success',
            'outdate'
        );

        if (isset($side_order['summary'])) {
            $summary = $channel->getSummary();
        }

        if (isset($side_order['sumcalls'])) {
            $sumcalls = $channel->getSumcalls();
        }

        if (isset($side_order['sponsors'])) {
            // Patrocinadores del nodo
            $sponsors = \Goteo\Model\Sponsor::getList($id);
        }


        $this->contextVars('channel/', [
            'channel'     => $channel,
            'side_order' => $side_order,
            'summary' => $summary,
            'sumcalls' => $sumcalls,
            'sponsors' => $sponsors,
            'categories' => $categories,
            'types' => $types,
            'url_project_create' => '/channel/' . $id . '/create'
        ]);
    }

    /**
     * TODO:
     * @param  [type] $id   [description]
     * @param  string $page [description]
     * @return [type]       [description]
     */
    public function indexAction($id)
    {
        $this->setChannelContext($id);

        // orden de los elementos en portada
        $order = Home::getAll($id);
        $side_order = Home::getAllSide($id);


        $hide_promotes = false;

        // Proyectos destacados primero para saber si lo meto en el buscador o no
        if (isset($order['promotes']) || isset($side_order['searcher'])) {
            $promotes  = Promote::getAll(true, $id);
        }

        // padrinos
        if (isset($order['patrons'])) {
            $patrons  =  $patrons = Patron::getInHome();
        }

        // Laterales
        // ---------------------
        if (isset($side_order['searcher'])) {
            // Selector proyectos: los destacados, los grupos de discover y los retornos

            if (!empty($promotes)) {
                $searcher['promote'] = Text::get('node-side-searcher-promote');

            }
        }

        // resto de centrales
        // entradas de blog
        if (isset($order['posts'])) {
            // entradas en portada
            $posts     = Post::getAll('home', $id);
        }

        // Convocatorias destacadas
        if (isset($order['calls'])) {
            $calls     = Call::getActive(3); // convocatorias en modalidad 1; inscripcion de proyectos
            $campaigns = Call::getActive(4); // convocatorias en modalidad 2; repartiendo capital riego
        }

        return $this->viewResponse(
            'channel/index',
            array(

                // centrales
                'order'    => $order,
                    'posts'    => $posts,
                    'promotes' => $promotes,
                    'calls'    => array('calls'=>$calls, 'campaigns'=>$campaigns),
                    'patrons' => $patrons,

                // laterales
                    'searcher' => $searcher,

                // los ocultos del discover
                'discover' => $discover

            )
        );
    }

    /**
     * All channel projects
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function listProjectsAction($id, $type = 'available', $category = null, Request $request)
    {
        $this->setChannelContext($id);

        $limit = 10;
        $filter = ['type' => $type];

        $title_text = $type === 'available' ? Text::get('regular-discover') : Text::get('node-side-searcher-'.$type);
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
     * @return [type]           [description]
     */
    public function createAction ($id, Request $request)
    {
        // Some context vars
        $this->contextVars('/', ['url_project_create' => '/channel/' . $id . '/create']);

        if (! ($user = Session::getUser()) ) {
            Session::store('jumpto', '/channel/' . $id . '/create');
            Application\Message::info(Text::get('user-login-required-to_create'));
            return $this->redirect(SEC_URL.'/user/login');
        }

        if ($request->request->get('action') != 'continue' || $request->request->get('confirm') != 'true') {
            $page = Page::get('howto');

             return $this->viewResponse('project/howto', array(
                        'action' => '/channel/' . $id . '/create',
                        'name' => $page->name,
                        'description' => $page->description,
                        'content' => $page->content
                    )
                 );
        }

        //Do the creation stuff (exception will be throwed on fail)
        $project = Project::createNewProject(Session::getUser(), $id);
        return $this->redirect('/project/edit/'.$project->id);
    }

}
