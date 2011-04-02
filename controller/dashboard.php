<?php

namespace Goteo\Controller {

    use Goteo\Library\Content,
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

            include 'view/dashboard.html.php';

        }
        
    }
    
}