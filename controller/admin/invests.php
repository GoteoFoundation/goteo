<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Library\Message,
        Goteo\Library\Paypal,
        Goteo\Library\Tpv,
        Goteo\Model;

    class Invests {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            // métodos de pago
            $methods = Model\Invest::methods();
            // estados del proyecto
            $status = Model\Project::status();
            // estados de aporte
            $investStatus = Model\Invest::status();
            // listado de proyectos
            $projects = Model\Invest::projects(false, $_SESSION['admin_node']);
            // usuarios cofinanciadores
            $users = Model\Invest::users(true, $_SESSION['admin_node']);
            // campañas que tienen aportes
            $calls = Model\Invest::calls();
            // extras
            $types = array(
                'donative' => 'Solo los donativos',
                'anonymous' => 'Solo los anónimos',
                'manual' => 'Solo los manuales',
                'campaign' => 'Solo con riego',
            );


            // detalles del aporte
            if ($action == 'details') {

                $invest = Model\Invest::get($id);
                $project = Model\Project::get($invest->project);
                $userData = Model\User::get($invest->user);

                if (!empty($invest->droped)) {
                    $droped = Model\Invest::get($invest->droped);
                } else {
                    $droped = null;
                }

                if ($project->node != $_SESSION['admin_node']) {
                    throw new Redirection('/admin/invests');
                }

                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'invests',
                        'file' => 'details',
                        'invest' => $invest,
                        'project' => $project,
                        'user' => $userData,
                        'status' => $status,
                        'investStatus' => $investStatus,
                        'droped' => $droped,
                        'calls' => $calls
                    )
                );
            }

            // listado de aportes
            if ($filters['filtered'] == 'yes') {

                if (!empty($filters['calls']))
                    $filters['types'] = '';

                $list = Model\Invest::getList($filters, $_SESSION['admin_node']);
            } else {
                $list = array();
            }

             $viewData = array(
                    'folder' => 'invests',
                    'file' => 'list',
                    'list'          => $list,
                    'filters'       => $filters,
//                    'users'         => $users,
                    'projects'      => $projects,
                    'calls'         => $calls,
                    'methods'       => $methods,
                    'types'         => $types,
//                    'status'        => $status,
                    'investStatus'  => $investStatus
                );

            return new View(
                'view/admin/index.html.php',
                $viewData
            );

        }

    }

}
