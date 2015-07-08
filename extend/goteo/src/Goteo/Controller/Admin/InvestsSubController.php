<?php
/**
 * Gestion parcial de aportes para nodos tipo superadmin (barcelona)
 */
namespace Goteo\Controller\Admin;

use Goteo\Library\Feed,
    Goteo\Application\Message,
	Goteo\Application\Config,
    Goteo\Model;

class InvestsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'details' => 'Detalles del aporte',
    );


    static protected $label = 'Aportes';


    protected $filters = array (
      'methods' => '',
      'status' => 'all',
      'investStatus' => 'all',
      'projects' => '',
      'name' => '',
      'calls' => '',
      'types' => '',
    );

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only non-central nodes with superadmins roles are allowed here
        if( Config::isMasterNode($node) || ! $user->hasRoleInNode($node, ['superadmin', 'root']) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function detailsAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('details', $id, $this->getFilters(), $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


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
        if (!empty($filters['calls']))
            $filters['types'] = '';

        $limit = 25;
        $total = Model\Invest::getList($filters, $node, 0, 0, true);
        $total_money = Model\Invest::getList($filters, $node, 0, 0, 'money');
        $list = Model\Invest::getList($filters, $node, $this->getGet('pag') * $limit, $limit);

        return array(
                'template' => 'admin/invests/list',
                'list'          => $list,
                'filters'       => $filters,
                'projects'      => $projects,
                'calls'         => $calls,
                'methods'       => $methods,
                'types'         => $types,
                'investStatus'  => $investStatus,
                'limit' => $limit,
                'total' => $total,
                'total_money' => $total_money
            );

    }

}

