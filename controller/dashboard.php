<?php

namespace Goteo\Controller {

    use Goteo\Core\Error,
        Goteo\Core\View,
        Goteo\Library\Content,
        Goteo\Model;

    class Dashboard extends \Goteo\Core\Controller {

        /*
         *  La manera de obtener el id del usuario validado cambiará al tener la session
         */
        public function index () {
            Model\User::restrict(); 

			$user = $_SESSION['user']->id;
			
            $message = "Hola {$user}<br />";

            if ($id == 'root') {
                $message .= '<a href="/texts">Gestión de textos</a>';
            }

            $projects = Model\Project::ofmine($user);

            $status = Model\Project::status();

            return new View (
                'view/dashboard.html.php',
                array(
                    'message' => $message,
                    'projects' => $projects,
                    'status' => $status
                )
            );

        }

        
    }
    
}