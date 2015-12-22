<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
/**
 * Gestion de recompensas de aportes solo para nodo central
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Message,
	Goteo\Library\Feed,
    Goteo\Model;

class RewardsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'rewards-lb-list',
      'edit' => 'rewards-lb-edit',
    );


    static protected $label = 'rewards-lb';


    protected $filters = array (
      'project' => '',
      'name' => '',
      'status' => '',
      'friend' => '',
    );


    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node allowed here
        if( ! Config::isMasterNode($node) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }

    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }

    public function fulfillAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('fulfill', $id, $this->getFilters(), $subaction));
    }

    public function unfillAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('unfill', $id, $this->getFilters(), $subaction));
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
            $projectData = $invest->getProject();
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
                $invest->address = array(
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
                    'template' => 'admin/rewards/edit',
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
        $limit = 20;
        $list = Model\Project\Reward::getChosen($filters, $this->getGet('pag') * $limit, $limit);
        $total = Model\Project\Reward::getChosen($filters, 0, 0, true);

        return array(
                'template' => 'admin/rewards/list',
                'list'          => $list,
                'filters'       => $filters,
                'projects'      => $projects,
                'status'        => $status,
                'limit' => $limit,
                'total' => $total

        );

    }

}

