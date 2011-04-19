<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\View,
        Goteo\Model,
        Goteo\Library\Worth;

    class Invest extends \Goteo\Core\Controller {

        /*
         *  La manera de obtener el id del usuario validado cambiará al tener la session
         */
        public function index ($project = null) {

            if (empty($project))
                throw new Redirection('/project/explore');

            $projectData = Model\Project::get($project);

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $errors = array();

                if (empty($_POST['amount'])) {
                    $errors[] = 'Indicar la cantidad del aporte';
                }
                else {
                    $invest = new Model\Invest(
                        array(
                            'amount' => $_POST['amount'],
                            'user' => $_SESSION['user']->id,
                            'project' => $project,
                            'status' => 0,
                            'invested' => date('Y-m-d'),
                            'anonymous' => $_POST['anonymous'],
                            'resign' => $_POST['resign']
                        )
                    );

                    if ($invest->save($errors)) {

                        // añadir recompensas que ha elegido
                        if (!empty($invest->id) && $invest->resign != 1) {
                            foreach ($_POST as $key=>$value) {
                                if (substr($key, 0, strlen('reward_')) == 'reward_') {
                                    //@TODO solo si el importe de la aportación es mayor o igual al importe mínimo para la recompensa
                                    if ($invest->setReward($value))
                                        $invest->rewards[] = $value;
                                }
                            }
                        }

                        $message = 'Gracias. Se ha realizado el aporte ' . $invest->id . '<br />';

                        // la vista es la página de difundir el proyecto
                        $message .= '<pre>' . print_r($invest, 1) . '</pre>';
                    }
                }

			    if (!empty($errors))
                    $message .= 'Errores: ' . implode('.', $errors);
			}

            $worthcracy         = Worth::getAll();

            $viewData = array(
                    'message' => $message,
                    'user' => $user,
                    'project' => $projectData
                );

            return new View (
                'view/invest.html.php',
                $viewData
            );

        }





    }

}