<?php

namespace Goteo\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Goteo\Application\View;
use Goteo\Model;
use Goteo\Library\Text;
use Goteo\Application\Message;
use Goteo\Library\Listing;
use Goteo\Model\Category;
use Goteo\Model\Project;
use Goteo\Model\Icon;

class DiscoverController extends \Goteo\Core\Controller {

    public static $types = array(
            'popular',
            'recent',
            'success',
            'outdate',
            'archive',
            'fulfilled'
        );

    public function __construct() {
        //activamos la cache para todo el controlador index
        \Goteo\Core\DB::cache(true);

        //assign common variables to all views containing the word "discover/"
        View::getEngine()->useContext('discover/', [
            'categories' => Category::getList(),   // categorias que se usan en proyectos
            'locations' => Project::getProjLocs(),  //localizaciones de royectos
            'rewards' => Icon::getList() // iconos que se usan en proyectos
            ]);
    }

    /*
     * Descubre proyectos, página general
     */
    public function indexAction () {

        $types = self::$types;

        $viewData = array(
            'lists' => [],
            'params' => [
                'category' => [],
                'location' => [],
                'reward' => [],
            ]
        );

        if (\Goteo\Application\Config::isNode()) {
            $types[] = 'others';
        }

        // cada tipo tiene sus grupos
        foreach ($types as $type) {
            $projects = Model\Project::published($type, 33);
            if (empty($projects)) continue;
            // random para exitosos y retorno cumplido
            if ($type == 'success' || $type == 'fulfilled') shuffle ($projects);

            $viewData['lists'][$type] = Listing::get($projects);
        }

        return new Response(View::render('discover/index', $viewData));

    }

    /*
     * Descubre proyectos, resultados de búsqueda
     */
    public function resultsAction ($category = null, $name = null, Request $request) {

        $message = '';
        $results = null;
        $query                         = $request->query->get('query');
        if(empty($query))    $query    = $request->request->get('query'); //POST

        // $params['status']              = $request->request->get('status');
        $params = [];

        $params['query']   =  strip_tags($query);
        foreach(array('category', 'location', 'reward') as $key) {
            if($request->request->has($key)) {
                $val = $request->request->get($key);
            }
            elseif($key === 'category') {
                if(empty($category)) $val = $request->request->get('category');
            }
            else {
                continue;
            }
            $params[$key] = (is_array($val) ? $val : [$val]);
            if(in_array('all', $val)) $params[$key] = array();
        }

        // print_r($params);die;
        if($params) {
            $results = \Goteo\Library\Search::params($params, false, 33);
        }
        else {
            return new RedirectResponse('/discover');
        }

        return new Response(View::render('discover/results', [
                'message' => $message,
                'results' => $results,
                'query'   => $query,
                'params'  => $params
            ]));
    }

    /*
     * Descubre proyectos, ver todos los de un tipo
     */
    public function viewAction ($type = 'all', Request $request) {
        $types = self::$types;

        $types[] = 'all';
        if (\Goteo\Application\Config::isNode()) {
            $types[] = 'others';
        }

        if (!in_array($type, $types)) {
            return new RedirectResponse('/discover');
        }

        $viewData = array();

        // segun el tipo cargamos el título de la página
        $viewData['title'] = Text::get('discover-group-'.$type.'-header');

        $page = max(1, (int) $request->request->get('page'));
        $items_per_page = 9;
        $viewData['list'] = Model\Project::published($type, $items_per_page, $page, $pages);
        $viewData['pages'] = $pages;
        $viewData['currentPage'] = $page;

        // segun el tipo cargamos la lista
        if ($request->request->has('list')) {
            return new Response(View::render('discover/list', $viewData));

        } else {

            // random para retorno cumplido
            if ($type == 'fulfilled') {
                shuffle($viewData['list']);
            }

            return new Response(View::render('discover/view', $viewData));

        }
    }

    /*
     * proyectos recomendados por usuario padrino (patron)
     */
    public function patronAction ($user) {

        $viewData = array();

        // título de la página
        $viewData['title'] = Text::get('discover-patron-header', $user);

        // segun el tipo cargamos la lista
        $viewData['list']  = Model\Patron::getList($user);

        return new Response(View::render('discover/patron', $viewData));

    }

}

