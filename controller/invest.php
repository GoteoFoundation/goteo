<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Model,
        Goteo\Library\Paypal,
        Goteo\Library\Tpv;

    class Invest extends \Goteo\Core\Controller {

        /*
         *  La manera de obtener el id del usuario validado cambiará al tener la session
         */
        public function index ($project = null) {
            if (empty($project))
                throw new Redirection('/discover', Redirection::TEMPORARY);

            $message = '';

            $projectData = Model\Project::get($project);
            $methods = Model\Invest::methods();

            // si no está en campaña no pueden esta qui ni de coña
            if ($projectData->status != 3) {
                throw new Redirection('/project/'.$project, Redirection::TEMPORARY);
            }

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $errors = array();
                $los_datos = $_POST;

                if (empty($_POST['method'])) {
                    $_POST['method'] = 'paypal';
                }

                if (empty($_POST['amount'])) {
                    $_POST['amount'] = 10;
                }

                if ($projectData->owner == $_SESSION['user']->id || empty($_POST['email'])) {
                    throw new Redirection("/project/$project/invest/?confirm=fail", Redirection::TEMPORARY);
                }

                // añadir recompensas que ha elegido
                
                $rewards = array();
                if (isset($_POST['resign']) && $_POST['resign'] == 1) {
                    // renuncia a las recompensas, bien por el/ella
                } else {
                    foreach ($_POST as $key=>$value) {
                        if (substr($key, 0, strlen('reward_')) == 'reward_') {

                            $id = \str_replace('reward_', '', $key);

                            //no darle las recompensas que no entren en el rango del aporte por mucho que vengan marcadas
                            if ($projectData->individual_rewards[$id]->amount <= $_POST['amount']) {
                                $rewards[] = $id;
                            }
                        }
                    }
                }

                // dirección de envio para las recompensas
                $address = array(
                    'address'  => $_POST['address'],
                    'zipcode'  => $_POST['zipcode'],
                    'location' => $_POST['location'],
                    'country'  => $_POST['country']
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
                        'method' => $_POST['method'],
                        'status' => 0,
                        'invested' => date('Y-m-d'),
                        'anonymous' => $_POST['anonymous'],
                        'resign' => $_POST['resign']
                    )
                );
                $invest->rewards = $rewards;
                $invest->address = (object) $address;

                if ($invest->save($errors)) {

                    switch($_POST['method']) {
                        case 'tpv':
                            // redireccion al tpv
                            Tpv::preapproval($invest, $errors);
                            die;
                            break;
                        case 'paypal':
                            // Petición de preapproval y redirección a paypal
                            Paypal::preapproval($invest, $errors);
                            die;
                            break;
                        case 'cash':
                            // En betatest aceptamos cash para pruebas
                            throw new Redirection("/project/$project/invest/?confirm=ok");
                            break;
                    }

                    // si seguimos aqui es que algo ha fallado
                    $errors[] = 'Algo ha fallado';
                }
			}

            throw new Redirection("/project/$project/invest");
        }


        public function confirmed ($project = null) {
            if (empty($project))
                throw new Redirection('/discover', Redirection::TEMPORARY);

            // no hay que hacer nada mas, aporte listo para cargar cuando sea
            // mandarlo a la pagina de gracias
            throw new Redirection("/project/$project/invest/?confirm=ok", Redirection::TEMPORARY);
        }

        /*
         * @params project id del proyecto
         * @params is id del aporte
         */
        public function fail ($project = null, $id = null) {
            if (empty($project))
                throw new Redirection('/discover', Redirection::TEMPORARY);

            if (empty($id))
                throw new Redirection("/project/$project/invest", Redirection::TEMPORARY);

            // quitar el preapproval y cancelar el aporte
            $invest = Model\Invest::get($id);
            $invest->cancel();

            // mandarlo a la pagina de aportar para que lo intente de nuevo
            throw new Redirection("/project/$project/invest/?confirm=fail", Redirection::TEMPORARY);
        }


    }

}