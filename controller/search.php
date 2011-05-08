<?php

namespace Goteo\Controller {

    use Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model\Project;

    class Search extends \Goteo\Core\Controller {

        /*
         *  Buscador base texto
         */
        public function index () {

            $message = '';
            $results = null;

			if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['query'])) {
                $errors = array();
                $los_datos = $_GET;

                $message = "Buscando <strong>{$_GET['query']}</strong>";

                $results = \Goteo\Library\Search::text($_GET['query']);

			} else {
                throw new Redirection('/project/explore', Redirection::PERMANENT);
            }

            if (!empty($errors)) {
                $message .= 'Errores: ' . implode('.', $errors);
            }


            $viewData = array(
                    'message' => $message,
                    'results' => $results
                );

            return new View (
                'view/search.html.php',
                $viewData
            );
        }
    }
}