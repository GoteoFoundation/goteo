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
use Goteo\Application\Message;
use Goteo\Library\Feed;
use Goteo\Model\Project;
use Goteo\Model\Project\Reward;
use Goteo\Model\Invest;
use Goteo\Model\User;

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
    static public function isAllowed(User $user, $node) {
        // Only central node allowed here
        if( ! Config::isMasterNode($node) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function editAction($id = null, $subaction = null) {

        $invest = Invest::get($id);
        $projectData = $invest->getProject();
        $userData = User::getMini($invest->user);
        $status = Project::status();

        // si tratando post
        if ($this->isPost()) {
            $errors = array();
            if($this->hasPost('selected_reward'))
            {
                $r = $this->getPost('selected_reward');
                if (empty($r)) {
                    // renuncia a las recompensas, bien por el/ella!
                    $invest->rewards = array();
                } else {
                    // la recompensa:
                    $chosen = Reward::get($r);
                    if(!$chosen instanceof Reward) {
                        Message::error('la recompensa elegida no existe en la base de datos!');
                        return $this->redirect(['edit', $id]);
                    }
                    if($r!=$invest->rewards[0]->id&&!$chosen->available()) {
                        Message::error('la recompensa elegida está agotada!');
                        return $this->redirect(['edit', $id]);
                    }

                    $invest->rewards = array($chosen);
                }
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
                return $this->redirect(['edit', $id]);
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

    public function listAction($id = null, $subaction = null) {
        $filters = $this->getFilters();

        $status = array(
                    'nok' => 'Pendiente',
                    'ok'  => 'Cumplida'
                );

        // listado de aportes
        $limit = 20;
        $list = Reward::getChosen($filters, $this->getGet('pag') * $limit, $limit);
        $total = Reward::getChosen($filters, 0, 0, true);

        return array(
            'template' => 'admin/rewards/list',
            'list'          => $list,
            'filters'       => $filters,
            'status'        => $status,
            'limit' => $limit,
            'total' => $total

        );
    }

    public function fulfillAction($id = null, $subaction = null) {
        $sql = "UPDATE invest_reward SET fulfilled = 1 WHERE invest = ?";
        if (Invest::query($sql, array($id))) {
            Message::info('La recompensa se ha marcado como cumplido');
        } else {
            Message::error('Ha fallado al marcar la recompensa');
        }
        return $this->redirect();
    }

    public function unfillAction($id = null, $subaction = null) {
        $sql = "UPDATE invest_reward SET fulfilled = 0 WHERE invest = ?";
        if (Invest::query($sql, array($id))) {
            Message::info('La recompensa se ha desmarcado, ahora está pendiente');
        } else {
            message::Error('Ha fallado al desmarcar');
        }
        return $this->redirect();
    }

}
