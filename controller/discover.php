<?php

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Library\Text;

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
                'success' => Text::get('discover-group-success-header')
            );
            $viewData['types'] = array(
                'popular' => Model\Project::published('popular', 3),
                'outdate' => Model\Project::published('outdate', 3),
                'recent' => Model\Project::published('recent', 3),
                'success' => Model\Project::published('success', 3)
            );

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

                $query = $_GET['query']; // busqueda de texto

                $message = "Buscando <strong>{$query}</strong>";

                $results = \Goteo\Library\Search::text($query);

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

            if (!in_array($type, array('popular', 'outdate', 'recent', 'success', 'all'))) {
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

    }
    
}