<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Library\Message,
        Goteo\Model;

    class Accounts {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            // visor de logs
            if ($action == 'viewer') {
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'accounts',
                        'file' => 'viewer'
                    )
                );
            }

            // cargamos los filtros
            $filters = array();
            $fields = array('filtered', 'methods', 'investStatus', 'projects', 'users', 'calls', 'review', 'date_from', 'date_until');
            foreach ($fields as $field) {
                $filters[$field] = (string) $_GET[$field];
            }

            if (!isset($filters['investStatus'])) $filters['investStatus'] = 'all';

            // tipos de aporte
            $methods = Model\Invest::methods();
            // estados del proyecto
            $status = Model\Project::status();
            // estados de aporte
            $investStatus = Model\Invest::status();
            // listado de proyectos
            $projects = Model\Invest::projects();
            // usuarios cofinanciadores
            $users = Model\Invest::users(true);
            // campañas que tienen aportes
            $calls = Model\Invest::calls();

            // filtros de revisión de proyecto
            $review = array(
                'collect' => 'Recaudado',
                'paypal'  => 'Rev. PayPal',
                'tpv'     => 'Rev. TPV',
                'online'  => 'Pagos Online'
            );


            /// detalles de una transaccion
            if ($action == 'details') {
                $invest = Model\Invest::get($id);
                $project = Model\Project::get($invest->project);
                $userData = Model\User::get($invest->user);
                return new View(
                    'view/admin/index.html.php',
                    array(
                        'folder' => 'accounts',
                        'file' => 'details',
                        'invest'=>$invest,
                        'project'=>$project,
                        'user'=>$userData,
                        'details'=>$details,
                        'status'=>$status,
                        'investStatus'=>$investStatus
                    )
                );
            }

            // listado de aportes
            if ($filters['filtered'] == 'yes') {
                $list = Model\Invest::getList($filters);
            } else {
                $list = array();
            }

             $viewData = array(
                    'folder' => 'accounts',
                    'file' => 'list',
                    'list'          => $list,
                    'filters'       => $filters,
                    'users'         => $users,
                    'projects'      => $projects,
                    'calls'         => $calls,
                    'review'        => $review,
                    'methods'       => $methods,
                    'status'        => $status,
                    'investStatus'  => $investStatus,
                    'errors'        => $errors
                );

            return new View(
                'view/admin/index.html.php',
                $viewData
            );

        }

    }

}
