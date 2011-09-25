<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Model,
		Goteo\Library\Feed,
		Goteo\Library\Text,
        Goteo\Library\Message,
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

                if (empty($_POST['amount'])) {
                    Message::Error('Tienes que poner el importe');
                    throw new Redirection("/project/$project/invest/?confirm=fail", Redirection::TEMPORARY);
                }

                // dirección de envio para las recompensas
                // o datoas fiscales del donativo
                $address = array(
                    'name'     => $_POST['fullname'],
                    'nif'      => $_POST['nif'],
                    'address'  => $_POST['address'],
                    'zipcode'  => $_POST['zipcode'],
                    'location' => $_POST['location'],
                    'country'  => $_POST['country']
                );

                if ($projectData->owner == $_SESSION['user']->id) {
                    Message::Error('Eres el autor del proyecto, no puedes aportar personalmente a tu propio proyecto.');
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

                // insertamos los datos personales del usuario si no tiene registro aun
                Model\User::setPersonal($_SESSION['user']->id, $address, false);

                // @TODO, cuenta paypal del usuario o su email
                $invest = new Model\Invest(
                    array(
                        'amount' => $_POST['amount'],
                        'user' => $_SESSION['user']->id,
                        'project' => $project,
                        'account' => $_SESSION['user']->email,
                        'method' => $_POST['method'],
                        'status' => '-1',               // aporte en proceso
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
                            if (Tpv::preapproval($invest, $errors)) {
                                die;
                            } else {
                                Message::Error('Ha fallado la pasarela del banco. ' . implode(',', $errors));
                            }
                            break;
                        case 'paypal':
                            // Petición de preapproval y redirección a paypal
                            if (Paypal::preapproval($invest, $errors)) {
                                die;
                            } else {
                                Message::Error('Ha fallado la peticion paypal. ' . implode(',', $errors));
                            }
                            break;
                        case 'cash':
                            $invest->setStatus('0');
                            // En betatest aceptamos cash para pruebas
                            throw new Redirection("/project/$project/invest/?confirm=ok");
                            break;
                    }
                } else {
                    Message::Error('Ha habido algun problema al inicializar la transacción');
                }
			} else {
                Message::Error('No se han recibido los datos necesarios');
            }

            throw new Redirection("/project/$project/invest/?confirm=fail");
            //throw new Redirection("/project/$project/invest");
        }


        public function confirmed ($project = null, $invest = null) {
            if (empty($project) || empty($invest)) {
                Message::Error('Ha llegado una confirmación paypal sin proyecto o sin Id de aporte');
                throw new Redirection('/discover', Redirection::TEMPORARY);
            }

            // hay que cambiarle el status a 0
            $confirm = Model\Invest::get($invest);
            $confirm->setStatus('0');

            if ($confirm->method == 'paypal') {
                $projectData = Model\Project::getMini($project);

                /*
                 * Evento Feed
                 */
                $log = new Feed();
                $log->title = 'Aporte PayPal';
                $log->url = '/admin/invests';
                $log->type = 'money';
                $log_text = "%s ha aportado %s al proyecto %s mediante PayPal";
                $items = array(
                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                    Feed::item('money', $confirm->amount.' &euro;'),
                    Feed::item('project', $projectData->name, $projectData->id)
                );
                $log->html = \vsprintf($log_text, $items);
                $log->add($errors);

                // evento público
                $log->title = $_SESSION['user']->name;
                $log->url = '/user/profile/'.$_SESSION['user']->id;
                $log->image = $_SESSION['user']->avatar->id;
                $log->scope = 'public';
                $log->type = 'community';
                $log->html = Text::html('feed-invest',
                                    Feed::item('money', $confirm->amount.' &euro;'),
                                    Feed::item('project', $projectData->name, $projectData->id));
                $log->add($errors);

                unset($log);
            }

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