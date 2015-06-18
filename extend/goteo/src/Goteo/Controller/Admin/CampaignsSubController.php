<?php
/**
 * Convocatorias en portada
 */
namespace Goteo\Controller\Admin;

use Goteo\Library\Feed,
    Goteo\Application\Message,
	Goteo\Application\Config,
    Goteo\Model;

class CampaignsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'details' => 'Detalles del aporte',
      'update' => 'Cambiando el estado al aporte',
      'add' => 'Nueva convocatoria destacada',
      'move' => 'Reubicando el aporte',
      'execute' => 'Ejecución del cargo',
      'cancel' => 'Cancelando aporte',
      'report' => 'Informe de proyecto',
      'viewer' => 'Viendo logs',
      'edit' => 'Editando Entrada',
      'translate' => 'Traduciendo Entrada',
      'reorder' => 'Ordenando las entradas en Portada',
      'footer' => 'Ordenando las entradas en el Footer',
      'projects' => 'Gestionando proyectos de la convocatoria',
      'admins' => 'Asignando administradores de la convocatoria',
      'posts' => 'Entradas de blog en la convocatoria',
      'conf' => 'Configurando la convocatoria',
      'dropconf' => 'Gestionando parte económica de la convocatoria',
    );


    static protected $label = 'Convocatorias destacadas';

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node and superadmins allowed here
        if( ! Config::isMasterNode($node) || !$user->hasRoleInNode($node, ['superadmin', 'root']) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->filters, $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->filters, $subaction));
    }


    public function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

        $status = Model\Call::status();

        $errors = array();

        $node = $this->node;

        if ($this->isPost()) {

            // objeto
            $campaign = new Model\Campaign(array(
                'id' => $id,
                'node' => $node,
                'call' => $this->getPost('call'),
                'order' => $this->getPost('order'),
                'active' => $this->getPost('active')
            ));

			if ($campaign->save($errors)) {
                switch ($this->getPost('action')) {
                    case 'add':
                        Message::info('Convocatoria destacada correctamente');

                        // parece que no se usa
                        // $callData = Model\Call::getMini($this->getPost('call'));

                        break;
                    case 'edit':
                        Message::info('Destacado actualizado correctamente');
                        break;
                }
			}
			else {

                Message::error(implode(', ', $errors));

                // Convocatorias disponibles
                $calls = Model\campaign::available($campaign->call, $node);


                switch ($this->getPost('action')) {
                    case 'add':
                        return array(
                                'folder' => 'campaigns',
                                'file' => 'edit',
                                'action' => 'add',
                                'campaign' => $campaign,
                                'status' => $status,
                                'calls' => $calls
                        );
                        break;
                    case 'edit':
                        return array(
                                'folder' => 'campaigns',
                                'file' => 'edit',
                                'action' => 'edit',
                                'campaign' => $campaign,
                                'status' => $status,
                                'calls' => $calls
                        );
                        break;
                }
			}
		}

        switch ($action) {
            case 'active':
                $set = $flag == 'on' ? true : false;
                Model\Campaign::setActive($id, $set);
                return $this->redirect('/admin/campaigns');
                break;
            case 'up':
                Model\Campaign::up($id, $node);
                return $this->redirect('/admin/campaigns');
                break;
            case 'down':
                Model\Campaign::down($id, $node);
                return $this->redirect('/admin/campaigns');
                break;
            case 'remove':
                if (Model\Campaign::delete($id)) {
                    // ok
                } else {
                    Message::error('No se ha podido quitar la convocatoria');
                }
                return $this->redirect('/admin/campaigns');
                break;
            case 'add':
                // siguiente orden
                $next = Model\Campaign::next($node);

                // Convocatorias disponibles disponibles
                $calls = Model\Campaign::available(null, $node);
                if (empty($calls)) {
                    Message::info('No hay convocatorias disponibles para destacar');
                    return $this->redirect('/admin/campaigns');
                }

                return array(
                        'folder' => 'campaigns',
                        'file' => 'edit',
                        'action' => 'add',
                        'campaign' => (object) array('order' => $next, 'node'=>$node),
                        'status' => $status,
                        'calls' => $calls
                );
                break;
            case 'edit':
                $campaign = Model\Campaign::get($id);
                // Convocatorias disponibles
                $calls = Model\Campaign::available($campaign->call, $node);

                return array(
                        'folder' => 'campaigns',
                        'file' => 'edit',
                        'action' => 'edit',
                        'campaign' => $campaign,
                        'status' => $status,
                        'calls' => $calls
                );
                break;
        }


        $setted = Model\Campaign::getAll(false, $node);

        return array(
                'folder' => 'campaigns',
                'file' => 'list',
                'setted' => $setted
        );

    }

}

