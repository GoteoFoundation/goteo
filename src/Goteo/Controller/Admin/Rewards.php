<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    class Rewards {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            switch ($action)  {
                case 'fulfill':
                    $sql = "UPDATE invest_reward SET fulfilled = 1 WHERE invest = ?";
                    if (Model\Invest::query($sql, array($id))) {
                        Message::Info('La recompensa se ha marcado como cumplido');
                    } else {
                        Message::Error('Ha fallado al marcar la recompensa');
                    }
                    throw new Redirection('/admin/rewards');
                    break;
                case 'unfill':
                    $sql = "UPDATE invest_reward SET fulfilled = 0 WHERE invest = ?";
                    if (Model\Invest::query($sql, array($id))) {
                        Message::Info('La recompensa se ha desmarcado, ahora está pendiente');
                    } else {
                        message::Error('Ha fallado al desmarcar');
                    }
                    throw new Redirection('/admin/rewards');
                    break;
            }

            // edicion
            if ($action == 'edit' && !empty($id)) {

                $invest = Model\Invest::get($id);
                $projectData = Model\Project::get($invest->project);
                $userData = Model\User::getMini($invest->user);
                $status = Model\Project::status();

                // si tratando post
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {

                    $errors = array();

                    // la recompensa:
                    $chosen = $_POST['selected_reward'];
                    if (empty($chosen)) {
                        // renuncia a las recompensas, bien por el/ella!
                        $invest->rewards = array();
                    } else {
                        $invest->rewards = array($chosen);
                    }

                    $invest->anonymous = $_POST['anonymous'];

                    // dirección de envio para la recompensa
                    // y datos fiscales por si fuera donativo
                    $invest->address = (object) array(
                        'name'     => $_POST['name'],
                        'nif'      => $_POST['nif'],
                        'address'  => $_POST['address'],
                        'zipcode'  => $_POST['zipcode'],
                        'location' => $_POST['location'],
                        'country'  => $_POST['country'],
                        'regalo'   => $_POST['regalo'],
                        'namedest' => $_POST['namedest'],
                        'emaildest'=> $_POST['emaildest']
                    );


                    if ($invest->update($errors)) {
                        Message::Info('Se han actualizado los datos del aporte: recompensa y dirección');
                        throw new Redirection('/admin/rewards');
                    } else {
                        Message::Error('No se han actualizado correctamente los datos del aporte. ERROR: '.implode(', ', $errors));
                    }

                }

                return new View(
                    'admin/index.html.php',
                    array(
                        'folder' => 'rewards',
                        'file' => 'edit',
                        'invest'   => $invest,
                        'project'  => $projectData,
                        'user'  => $userData,
                        'status'   => $status
                    )
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


            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'rewards',
                    'file' => 'list',
                    'list'          => $list,
                    'filters'       => $filters,
                    'projects'      => $projects,
                    'status'        => $status
                )
            );

        }

    }

}
