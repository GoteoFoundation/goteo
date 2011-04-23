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
                    // añadir recompensas que ha elegido
                    $rewards = array();
                    if (!empty($invest->id) && $invest->resign != 1) {
                        foreach ($_POST as $key=>$value) {
                            if (substr($key, 0, strlen('reward_')) == 'reward_')
                                $rewards[] = $value;
                        }
                    }

                    // @TODO, cuenta paypal del usuario o su email
                    $invest = new Model\Invest(
                        array(
                            'amount' => $_POST['amount'],
                            'user' => $_SESSION['user']->id,
                            'project' => $project,
                            'account' => 'julian_1302552287_per@gmail.com',
                            'status' => 0,
                            'invested' => date('Y-m-d'),
                            'anonymous' => $_POST['anonymous'],
                            'resign' => $_POST['resign'],
                            'rewards' => $rewards
                        )
                    );

                    if ($invest->save($errors)) {
                        // Petición de preapproval y a paypal
                        Paypal::preapproval($invest);
                        die; // listo porque eso va a saltar a paypal o a un error
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

        /*
         * @params project id del proyecto
         * @params is id del aporte
         */
        public function fail ($project = null, $id = null) {
            if (empty($_SESSION['user']))
                throw new Redirection ('/login', Redirection::TEMPORARY);

            if (empty($project))
                throw new Redirection('/project/explore', Redirection::TEMPORARY);

            if (empty($id))
                throw new Redirection('/invest/' . $project, Redirection::TEMPORARY);

            // quitar el preapproval y cancelar el aporte, mandarlo a la pagina de aportar para que lo intente de nuevo
            $invest = Model\Invest::get($id);
            $invest->cancelPreapproval();

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