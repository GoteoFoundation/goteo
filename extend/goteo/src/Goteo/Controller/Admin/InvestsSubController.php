<?php

namespace Goteo\Controller\Admin;

use Goteo\Library\Feed,
	Goteo\Application\Message,
    Goteo\Model;

class InvestsSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null, $filters = array()) {

        $node = $this->node;

        // métodos de pago
        $methods = Model\Invest::methods();
        // estados del proyecto
        $status = Model\Project::status();
        // estados de aporte
        $investStatus = Model\Invest::status();
        // listado de proyectos
        $projects = Model\Invest::projects(false, $node);
        // usuarios cofinanciadores
        $users = Model\Invest::users(true);
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

            if ($project->node != $node) {
                Message::error("Node [$node] not valid");
                return $this->redirect('/admin/invests');
            }

            return array(
                    'folder' => 'invests',
                    'file' => 'details',
                    'invest' => $invest,
                    'project' => $project,
                    'user' => $userData,
                    'status' => $status,
                    'investStatus' => $investStatus,
                    'droped' => $droped,
                    'calls' => $calls
            );
        }

        // listado de aportes
        if ($filters['filtered'] == 'yes') {

            if (!empty($filters['calls']))
                $filters['types'] = '';

            $list = Model\Invest::getList($filters, $node, 999);
        } else {
            $list = array();
        }

        return array(
                'folder' => 'invests',
                'file' => 'list',
                'list'          => $list,
                'filters'       => $filters,
                'projects'      => $projects,
                'users'         => $users,
                'calls'         => $calls,
                'methods'       => $methods,
                'types'         => $types,
                'investStatus'  => $investStatus
            );

    }

}

