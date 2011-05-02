<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\View,
        Goteo\Model;

    class Dashboard extends \Goteo\Core\Controller {

        /*
         *  Muy guarro para poder moverse mientras desarrollamos
         */
        public function index ($section = null) {
			$user = $_SESSION['user']->id;

            $message = "Hola {$user}<br />";

            //@FIXME!! esto también irá con el ACL
            if ($_SESSION['user']->role == 1) {
                $message .= '<a href="/admin">Ir al panel de administración</a><br />';
            }


            $projects = Model\Project::ofmine($user);

            $status = Model\Project::status();

            //mis cofinanciadores
            // array de usuarios con:
            //  foto, nombre, nivel, cantidad a mis proyectos, fecha ultimo aporte, nº proyectos que cofinancia
            $investors = array();
            foreach ($projects as $project) {
                foreach (Model\Invest::investors($project->id) as $key=>$investor) {
                    if (\array_key_exists($investor->user, $investors)) {
                        // ya está en el array, quiere decir que cofinancia este otro proyecto
                        // , añadir uno, sumar su aporte, actualizar la fecha
                        ++$investors[$investor->user]->projects;
                        $investors[$investor->user]->amount += $investor->amount;
                        $investors[$investor->user]->date = $investor->date;  // <-- @TODO la fecha mas actual
                    } else {
                        $investors[$investor->user] = (object) array(
                            'user' => $investor->user,
                            'name' => $investor->name,
                            'projects' => 1,
                            'avatar' => 'url',
                            'worth' => $investor->worth,
                            'amount' => $investor->amount,
                            'date' => $investor->date
                        );
                    }
                }
            }


            // comparten intereses
            $shares = Model\User\Interest::share($user);

            return new View (
                'view/dashboard.html.php',
                array(
                    'message' => $message,
                    'projects' => $projects,
                    'status' => $status,
                    'investors' => $investors,
                    'shares' => $shares
                )
            );

        }


    }

}