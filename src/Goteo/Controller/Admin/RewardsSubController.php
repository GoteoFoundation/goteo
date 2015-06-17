<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
	Goteo\Library\Feed,
    Goteo\Model;

class RewardsSubController extends AbstractSubController {

static protected $labels = array (
  'list' => 'Listando',
  'details' => 'Detalles del aporte',
  'update' => 'Cambiando el estado al aporte',
  'add' => 'Iniciando briefing',
  'move' => 'Moviendo a otro Nodo el proyecto',
  'execute' => 'Ejecución del cargo',
  'cancel' => 'Cancelando aporte',
  'report' => 'Informe',
  'viewer' => 'Viendo logs',
  'edit' => 'Gestionando recompensa',
  'translate' => 'Traduciendo Destacado',
  'reorder' => 'Ordenando los padrinos en Portada',
  'footer' => 'Ordenando las entradas en el Footer',
  'projects' => 'Informe Impulsores',
  'admins' => 'Asignando administradores del Canal',
  'posts' => 'Entradas de blog en la convocatoria',
  'conf' => 'Configuración de campaña del proyecto',
  'dropconf' => 'Gestionando parte económica de la convocatoria',
  'keywords' => 'Palabras clave',
  'view' => 'Apadrinamientos',
  'info' => 'Información de contacto',
  'send' => 'Comunicación enviada',
  'init' => 'Iniciando un nuevo envío',
  'activate' => 'Iniciando envío',
  'detail' => 'Viendo destinatarios',
  'dates' => 'Fechas del proyecto',
  'accounts' => 'Cuentas del proyecto',
  'images' => 'Imágenes del proyecto',
  'assign' => 'Asignando a una Convocatoria el proyecto',
  'open_tags' => 'Asignando una agrupación al proyecto',
  'rebase' => 'Cambiando Id de proyecto',
  'consultants' => 'Cambiando asesor del proyecto',
  'paypal' => 'Informe PayPal',
  'geoloc' => 'Informe usuarios Localizados',
  'calls' => 'Informe Convocatorias',
  'donors' => 'Informe Donantes',
  'top' => 'Top Cofinanciadores',
  'currencies' => 'Actuales ratios de conversión',
);


static protected $label = 'Recompensas';


    protected $filters = array (
  'project' => '',
  'name' => '',
  'status' => '',
  'friend' => '',
);


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->filters, $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->filters, $subaction));
    }


    public function process ($action = 'list', $id = null, $filters = array()) {

        switch ($action)  {
            case 'fulfill':
                $sql = "UPDATE invest_reward SET fulfilled = 1 WHERE invest = ?";
                if (Model\Invest::query($sql, array($id))) {
                    Message::info('La recompensa se ha marcado como cumplido');
                } else {
                    Message::error('Ha fallado al marcar la recompensa');
                }
                return $this->redirect('/admin/rewards');
                break;
            case 'unfill':
                $sql = "UPDATE invest_reward SET fulfilled = 0 WHERE invest = ?";
                if (Model\Invest::query($sql, array($id))) {
                    Message::info('La recompensa se ha desmarcado, ahora está pendiente');
                } else {
                    message::Error('Ha fallado al desmarcar');
                }
                return $this->redirect('/admin/rewards');
                break;
        }

        // edicion
        if ($action == 'edit' && !empty($id)) {

            $invest = Model\Invest::get($id);
            $projectData = Model\Project::get($invest->project);
            $userData = Model\User::getMini($invest->user);
            $status = Model\Project::status();

            // si tratando post
            if ($this->isPost() && $this->hasPost('update')) {

                $errors = array();

                // la recompensa:
                $chosen = $this->getPost('selected_reward');
                if (empty($chosen)) {
                    // renuncia a las recompensas, bien por el/ella!
                    $invest->rewards = array();
                } else {
                    $invest->rewards = array($chosen);
                }

                $invest->anonymous = $this->getPost('anonymous');

                // dirección de envio para la recompensa
                // y datos fiscales por si fuera donativo
                $invest->address = (object) array(
                    'name'     => $this->getPost('name'),
                    'nif'      => $this->getPost('nif'),
                    'address'  => $this->getPost('address'),
                    'zipcode'  => $this->getPost('zipcode'),
                    'location' => $this->getPost('location'),
                    'country'  => $this->getPost('country'),
                    'regalo'   => $this->getPost('regalo'),
                    'namedest' => $this->getPost('namedest'),
                    'emaildest'=> $this->getPost('emaildest')
                );


                if ($invest->update($errors)) {
                    Message::info('Se han actualizado los datos del aporte: recompensa y dirección');
                    return $this->redirect('/admin/rewards');
                } else {
                    Message::error('No se han actualizado correctamente los datos del aporte. ERROR: '.implode(', ', $errors));
                }

            }

            return array(
                    'folder' => 'rewards',
                    'file' => 'edit',
                    'invest'   => $invest,
                    'project'  => $projectData,
                    'user'  => $userData,
                    'status'   => $status
            );



        }



        // listado de proyectos
        $projects = Model\Invest::projects();

        $status = array(
                    'nok' => 'Pendiente',
                    'ok'  => 'Cumplida'

                );

        // listado de aportes
        if ($filters['filtered'] == 'yes') {
            $list = Model\Project\Reward::getChosen($filters);
        } else {
            $list = array();
        }


        return array(
                'folder' => 'rewards',
                'file' => 'list',
                'list'          => $list,
                'filters'       => $filters,
                'projects'      => $projects,
                'status'        => $status
        );

    }

}

