<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
	Goteo\Library\Feed,
    Goteo\Model;

class RewardsSubController extends AbstractSubController {

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

