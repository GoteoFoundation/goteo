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

            if ($projectData->owner == $_SESSION['user']->id)
                throw new Redirection('/dashboard', Redirection::TEMPORARY);

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $errors = array();
                $los_datos = $_POST;
                if (empty($_POST['email'])) {
                    $errors[] = 'Indicar la cuenta de paypal (o email)';
                }

                if (empty($_POST['amount'])) {
                    $errors[] = 'Indicar la cantidad del aporte';
                }

                if (empty($errors)) {
                    // añadir recompensas que ha elegido
                    $rewards = array();
                    if (isset($_POST['resign']) && $_POST['resign'] == 1) {
                        // renuncia a las recompensas, bien por el/ella
                    } else {
                        foreach ($_POST as $key=>$value) {
                            if (substr($key, 0, strlen('reward_')) == 'reward_')
                                $rewards[] = $value;
                        }
                    }

                    // dirección de envio para las recompensas
                    $address = array(
                        'address' => $_POST['address'],
                        'zipcode' => $_POST['zipcode'],
                        'location' => $_POST['location'],
                        'country' => $_POST['country']
                    );
                    // insertamos los datos personales del usuario si no tiene registro aun
                    Model\User::setPersonal($_SESSION['user']->id, $address, false);

                    // @TODO, cuenta paypal del usuario o su email
                    $invest = new Model\Invest(
                        array(
                            'amount' => $_POST['amount'],
                            'user' => $_SESSION['user']->id,
                            'project' => $project,
                            'account' => $_POST['email'],
                            'status' => 0,
                            'invested' => date('Y-m-d'),
                            'anonymous' => $_POST['anonymous'],
                            'resign' => $_POST['resign']
                        )
                    );
                    $invest->rewards = $rewards;
                    $invest->address = (object) $address;

                    if ($invest->save($errors)) {
                        // Petición de preapproval y redirección a paypal
                        Paypal::preapproval($invest, $errors);
                        // si no salta, vamos a tener los errores
                    }
                }
			}

            if (!empty($errors)) {
                $message .= 'Errores: ' . implode('.', $errors);
            }


            foreach ($projectData->individual_rewards as &$reward) {
                // si controla unidades de esta recompensa, mirar si quedan
                if ($reward->units > 0) {
                    $reward->taken = $reward->getTaken();
                    if ($reward->taken >= $reward->units) {
                        $reward->none = true;
                    } else {
                        $reward->none = false;
                    }
                } else {
                    $reward->none = false;
                }
            }


            $viewData = array(
                    'message' => $message,
                    'project' => $projectData,
                    'personal' => Model\User::getPersonal($_SESSION['user']->id)
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
            $invest->cancel();

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