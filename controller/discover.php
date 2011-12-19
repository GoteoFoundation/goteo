<?php

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Library\Text,
        Goteo\Library\Listing;

    class Discover extends \Goteo\Core\Controller {
    
        /*
         * Descubre proyectos, página general
         */
        public function index () {

            $viewData = array();
            $viewData['title'] = array(
                'popular' => Text::get('discover-group-popular-header'),
                'outdate' => Text::get('discover-group-outdate-header'),
                'recent'  => Text::get('discover-group-recent-header'),
                'success' => Text::get('discover-group-success-header'),
                'archive' => Text::get('discover-group-archive-header')
            );

            $viewData['lists'] = array();

            $types = array(
                'popular',
                'outdate',
                'recent',
                'success',
                'archive'
            );

            // cada tipo tiene sus grupos
            foreach ($types as $type) {
                $projects = Model\Project::published($type);
                if (empty($projects)) continue;
                $viewData['lists'][$type] = Listing::get($projects);
            }

            return new View(
                'view/discover/index.html.php',
                $viewData
             );

        }

        /*
         * Descubre proyectos, resultados de búsqueda
         */
        public function results ($category = null) {

            $message = '';
            $results = null;

            // si recibimos categoria por get emulamos post con un parametro 'category'
            if (!empty($category)) {
                $_POST['category'][] = $category;
            }

			if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['query']) && !isset($category)) {
                $errors = array();

                $params['query'] = $_GET['query']; // busqueda de texto

                $results = \Goteo\Library\Search::text($params['query']);

			} elseif (($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['searcher']) || !empty($category))) {

                // vamos montando $params con los 3 parametros y las opciones marcadas en cada uno
                $params = array('category'=>array(), 'location'=>array(), 'reward'=>array());

                foreach ($params as $param => $empty) {
                    foreach ($_POST[$param] as $key => $value) {
                        if ($value == 'all') {
                            $params[$param] = array();
                            break;
                        }
                        $params[$param][] = "'{$value}'";
                    }
                }

                $params['query'] = $_POST['query'];

                // para cada parametro, si no hay ninguno es todos los valores
                $results = \Goteo\Library\Search::params($params);

            } else {
                throw new Redirection('/discover', Redirection::PERMANENT);
            }

            return new View(
                'view/discover/results.html.php',
                array(
                    'message' => $message,
                    'results' => $results,
                    'query'   => $query,
                    'params'  => $params
                )
             );

        }
        
        /*
         * Descubre proyectos, ver todos los de un tipo
         */
        public function view ($type = 'all') {

            if (!in_array($type, array('popular', 'outdate', 'recent', 'success', 'archive', 'all'))) {
                throw new Redirection('/discover');
            }

            $viewData = array();

            // segun el tipo cargamos el título de la página
            $viewData['title'] = Text::get('discover-group-'.$type.'-header');

            // segun el tipo cargamos la lista
            $viewData['list']  = Model\Project::published($type);


            return new View(
                'view/discover/view.html.php',
                $viewData
             );

        }

        /*
         * Resultados para los
         */
        public function call () {

            // antenemos actualizados los datos de convocatoria
            $_SESSION['call'] = Model\Call::get($_SESSION['call']->id);

            if (!$_SESSION['call'] instanceof Model\Call || $_SESSION['assign_mode'] !== true || $_SESSION['call']->status >= 3) {
                throw new Redirection('/dashboard/calls/projects');
            } else {
                $call = $_SESSION['call'];
            }

            $viewData = array();

            // segun el tipo cargamos el título de la página
            $viewData['title'] = Text::get('discover-group-call-header') . ' ' . $_SESSION['call']->name;

            $message = 'Mostramos coincidencias con, ';

            // sacamos parametros de la convocatoria
            // para cada parametro, si no hay ninguno es todos los valores
            $params = array('category'=>array(), 'location'=>array(), 'reward'=>array());
            // categorias
            $message .= ' las categorias: ';
            foreach ($call->categories as $category) {
                $params['category'][] = "'{$category}'";
                $message .= $category . ', ';
            }

            // localizacion (separamos la localizacion de la convocatoria y las hacemos md5)
            if (!empty($call->call_location)) {
                $locations = \explode(',', $call->call_location);

                // solo ponemos las localidades que existan en proyectos
                $existing_locations = \Goteo\Library\Location::getList();

                $message .= '; Las localizaciones : ';
                foreach ($locations as $location ) {
                    $call_loc = md5(trim($location));
                    if (!empty($call_loc) && isset($existing_locations[$call_loc])) {
                        $params['location'][] = "'".$call_loc."'";
                        $message .= $location . ', ';
                    }
                }
            }

            // recompensas
            $message .= '; las recompensas: ';
            foreach ($call->icons as $icon) {
                $params['reward'][] = "'{$icon}'";
                $message .= $icon . ', ';
            }


            $viewData['message'] = $message;

            $viewData['list'] = \Goteo\Library\Search::params($params, true);

            return new View('view/discover/assign.html.php', $viewData);

        }

    }
    
}