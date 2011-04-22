<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Model,
        Goteo\Library\Worth,
        Goteo\Library\Paypal;

    class Invest extends \Goteo\Core\Controller {

        /*
         *  La manera de obtener el id del usuario validado cambiará al tener la session
         */
        public function index ($project = null) {

            if (empty($_SESSION['user']))
                throw new Redirection ('/login', Redirection::TEMPORARY);

            if (empty($project))
                throw new Redirection('/project/explore', Redirection::TEMPORARY);

            $message = '';

            $projectData = Model\Project::get($project);

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $errors = array();

                if (empty($_POST['email'])) {
                    $errors[] = 'Indicar la cuenta de paypal';
                }

                if (empty($_POST['amount'])) {
                    $errors[] = 'Indicar la cantidad del aporte';
                }

                if (empty($errors)) {
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

                    // Petición de preapproval y a paypal
                    Paypal::preapproval($invest->id, $_SESSION['user']->id, $project, $_POST['email'], $_POST['amount']);

                    }
                } else {
                    $message .= 'Errores: ' . implode('.', $errors);
                }
			}

            $viewData = array(
                    'message' => $message,
                    'project' => $projectData
                );

            return new View (
                'view/invest.html.php',
                $viewData
            );

        }


        public function confirmed ($project = null) {
            if (empty($_SESSION['user']))
                throw new Redirection ('/login', Redirection::TEMPORARY);

            if (empty($project))
                throw new Redirection('/project/explore', Redirection::TEMPORARY);

            // no hay que hacer nada mas, aporte listo para cargar cuando sea

            $message = 'Ya eres cofinanciador de este proyecto. Ayudanos a difundirlo.';

            $projectData = Model\Project::get($project);
            
            $viewData = array(
                    'message' => $message,
                    'project' => $projectData
                );

            return new View (
                'view/spread.html.php',
                $viewData
            );

        }

        public function fail ($project = null, $invest = null) {
            if (empty($_SESSION['user']))
                throw new Redirection ('/login', Redirection::TEMPORARY);

            if (empty($project))
                throw new Redirection('/project/explore', Redirection::TEMPORARY);

            if (empty($invest))
                throw new Redirection('/invest/' . $project, Redirection::TEMPORARY);

            // quitar el preapproval y cancelar el aporte, mandarlo a la pagina de aportar para que lo intente de nuevo
            Model\Invest::cancelPreapproval($invest, $project);

            $message = 'Aporte cancelado';

            $projectData = Model\Project::get($project);

            $viewData = array(
                    'message' => $message,
                    'project' => $projectData
                );

            return new View (
                'view/invest.html.php',
                $viewData
            );

        }


    }

}