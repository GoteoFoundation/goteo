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
      'update' => 'Cambiando el estado al aporte',
      'add' => 'Creando Idea',
      'move' => 'Reubicando el aporte',
      'execute' => 'Ejecución del cargo',
      'cancel' => 'Cancelando aporte',
      'report' => 'Informe de proyecto',
      'viewer' => 'Viendo logs',
      'edit' => 'Editando Idea',
      'translate' => 'Traduciendo Idea',
      'reorder' => 'Ordenando las entradas en Portada',
      'footer' => 'Ordenando las entradas en el Footer',
      'projects' => 'Gestionando proyectos de la convocatoria',
      'admins' => 'Asignando administradores de la convocatoria',
      'posts' => 'Entradas de blog en la convocatoria',
      'conf' => 'Configurando la convocatoria',
      'dropconf' => 'Gestionando parte económica de la convocatoria',
      'keywords' => 'Palabras clave',
      'view' => 'Gestión de retornos',
      'info' => 'Información de contacto',
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
        return call_user_func_array(array($this, 'process'), array('details', $id, $this->filters, $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->filters, $subaction));
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

