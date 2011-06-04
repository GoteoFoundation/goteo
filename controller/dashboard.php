<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model;

    class Dashboard extends \Goteo\Core\Controller {

        /*
         *  Muy guarro para poder moverse mientras desarrollamos
         */
        public function index ($section = null) {
            $user = $_SESSION['user']->id;

            // quitamos el stepped para que no nos lo coja para el siguiente proyecto que editemos
            if (isset($_SESSION['stepped'])) {
                unset($_SESSION['stepped']);
            }

            $message = "Hola {$user}<br />";

            if (ACL::check('/admin')) {
                $message .= '<a href="/admin">Ir al panel de administración</a><br />';
            }


            $projects = Model\Project::ofmine($user);

            $status = Model\Project::status();

            //mis cofinanciadores
            // array de usuarios con:
            //  foto, nombre, nivel, cantidad a mis proyectos, fecha ultimo aporte, nº proyectos que cofinancia
            $investors = array();
            foreach ($projects as $project) {

                // compruebo que puedo editar mis proyectos
                if (!ACL::check('/project/edit/'.$project->id)) {
                    ACL::allow('/project/edit/'.$project->id, '*', 'user', $user);
                }

                // y borrarlos
                if (!ACL::check('/project/delete/'.$project->id)) {
                    ACL::allow('/project/delete/'.$project->id, '*', 'user', $user);
                }

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
                            'avatar' => $investor->avatar,
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

        /*
         * Sección, Mi actividad
         * Opciones:
         *      'projects' los proyectos del usuario y a los que ha aportado,
         *      'comunity' relacion con la comunidad
         * 
         */
        public function activity ($option = 'summary', $action = 'view') {
            return new View (
                'view/dashboard/index.html.php',
                array(
                    'menu'    => self::menu(),
                    'message' => "Estas en tu actividad: $option",
                    'section' => __FUNCTION__,
                    'option'  => $option,
                    'action'  => $action
                )
            );

        }

        /*
         * Seccion, Mi perfil
         * Opciones:
         *      'public' perfil público (paso 1), 
         *      'personal' datos personales (paso 2),
         *      'config' configuracion (cambio de email y contraseña)
         *
         */
        public function profile ($option = 'profile', $action = 'edit') {
            return new View (
                'view/dashboard/index.html.php',
                array(
                    'menu'    => self::menu(),
                    'message' => "Estas en tu perfil: $option",
                    'section' => __FUNCTION__,
                    'option'  => $option,
                    'action'  => $action
                )
            );
        }


        /*
         * Seccion, Mi proyecto (actualmente en campaña o financiado, solo uno)
         * Opciones:
         *      'actualizaciones' blog del proyecto (ahora son como mensajes),
         *      'editar colaboraciones' para modificar los mensajes de colaboraciones (no puede editar el proyecto y ya estan publicados)
         *      'widgets' ofrece el código para poner su proyecto en otras páginas (vertical y horizontal)
         *      'licencia' el acuerdo entre goteo y el usuario, licencia cc-by-nc-nd, enlace al pdf
         *      'gestionar retornos' resumen recompensas/cofinanciadores/conseguido  y lista de cofinanciadores y recompensas esperadas
         *      'pagina publica' enlace a la página pública del proyecto
         *
         */
        public function projects ($option = 'summary', $action = 'view') {
            return new View (
                'view/dashboard/index.html.php',
                array(
                    'menu'    => self::menu(),
                    'message' => "Estas en tus proyectos: $option",
                    'section' => __FUNCTION__,
                    'option'  => $option,
                    'action'  => $action
                )
            );
        }

        /*
         * Salto al admin
         *
         */
        public function admin ($option = 'board') {
            if (ACL::check('/admin')) {
                throw new Redirection('/admin', Redirection::TEMPORARY);
            } else {
                throw new Redirection('/dashboard', Redirection::TEMPORARY);
            }
        }

        private static function menu() {

            $menu = array(
                'activity' => array(
                    'label'   => 'Mi actividad',
                    'options' => array (
                        'summary' => 'Resumen',
                        'wall'    => 'Mi muro'
                    )
                ),
                'profile' => array(
                    'label'   => 'Mi perfil',
                    'options' => array (
                        'profile'  => 'Editar perfil',
                        'personal' => 'Datos personales',
                        'access'   => 'Datos de acceso',
                    )
                )
            );

            // si tiene proyectos
            $projects = Model\Project::ofmine($_SESSION['user']->id);

            if (!empty($project)) {
                $menu['projects'] = array(
                    'label' => 'Mis proyectos',
                    'options' => array (
                        'summary'  => 'Resumen',
                        'updates'  => 'Actualizaciones',
                        'widgets'  => 'Widgets',
                        'contract' => 'Contrato',
                        'rewards'  => 'Gestionar retornos',
                        'supports' => 'Editar colaboraciones',
                        'preview'  => 'Página pública',
                    )
                );

            }

            // si tiene permiso para ir al admin
            if (ACL::check('/admin')) {
                $menu['admin'] = array(
                    'label'   => 'Administración',
                    'options' => array(
                        'board' => 'Ir al panel'
                    )
                );
            }

            return $menu;

        }



        }

}