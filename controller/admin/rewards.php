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
                        $invest->resign = true;
                        $invest->rewards = array();
                    } else {
                        $invest->resign = false;
                        $invest->rewards = array($chosen);
                    }


                    // dirección de envio para la recompensa
                    // y datos fiscales por si fuera donativo
                    $invest->address = (object) array(
                        'name'     => $_POST['name'],
                        'nif'      => $_POST['nif'],
                        'address'  => $_POST['address'],
                        'zipcode'  => $_POST['zipcode'],
                        'location' => $_POST['location'],
                        'country'  => $_POST['country']
                    );

                    
                    if ($invest->update($errors)) {
                        Message::Info('Se han actualizado los datos del aporte: recompensa y dirección');
                        throw new Redirection('/admin/rewards');
                    } else {
                        Message::Error('No se han actualizado correctamente los datos del aporte. ERROR: '.implode(', ', $errors));
                    }

                }

                return new View(
                    'view/admin/index.html.php',
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



            // listado

            // métodos de pago
            $methods = Model\Invest::methods();
            // estados de aporte
            $investStatus = Model\Invest::status();
            // listado de proyectos
            $projects = Model\Invest::projects();
            // campañas que tienen aportes
            $calls = Model\Invest::calls();

            // listado de aportes
            if ($filters['filtered'] == 'yes') {
                $list = Model\Invest::getList($filters);
            } else {
                $list = array();
            }


            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'rewards',
                    'file' => 'list',
                    'list'          => $list,
                    'filters'       => $filters,
                    'users'         => $users,
                    'projects'      => $projects,
                    'calls'         => $calls,
                    'methods'       => $methods,
                    'status'        => $status,
                    'investStatus'  => $investStatus,
                    'projects'=>$projects,
                    'filters' => $filters
                )
            );

        }

    }

}
