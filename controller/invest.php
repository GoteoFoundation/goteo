<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Model,
        Goteo\Library\Feed,
        Goteo\Library\Text,
        Goteo\Library\Mail,
        Goteo\Library\Template,
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
            //$methods = Model\Invest::methods();
            // Métodos habilitados en este entorno
            // en real solamente tpv, en beta también cash
            $methods = array(
                'tpv' => 'tpv',
                'paypal' => 'paypal'
            );

            // si no está en campaña no pueden esta qui ni de coña
            if ($projectData->status != 3) {
                throw new Redirection('/project/'.$project, Redirection::TEMPORARY);
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $errors = array();
                $los_datos = $_POST;
                $method = \strtolower($_POST['method']);

                if (!isset($methods[$method])) {
                    Message::Error(Text::get('invest-method-error'));
                    throw new Redirection("/project/$project/invest/?confirm=fail", Redirection::TEMPORARY);
                }

                if (empty($_POST['amount'])) {
                    Message::Error(Text::get('invest-amount-error'));
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
                    Message::Error(Text::get('invest-owner-error'));
                    throw new Redirection("/project/$project/invest/?confirm=fail", Redirection::TEMPORARY);
                }

                // añadir recompensas que ha elegido
                $chosen = $_POST['selected_reward'];
                if ($chosen == 0) {
                    // renuncia a las recompensas, bien por el/ella
                    $resign = true;
                    $reward = false;
                } else {
                    // mirar: si la recompensa es de tipo Reconocimiento, también es donativo
                    $rewardData = Model\Project\Reward::get($chosen);
                    $resign = ($rewardData->icon == 'thanks') ? true : false;
                    $reward = true;
                }

                // insertamos los datos personales del usuario si no tiene registro aun
                Model\User::setPersonal($_SESSION['user']->id, $address, false);

                $invest = new Model\Invest(
                    array(
                        'amount' => $_POST['amount'],
                        'user' => $_SESSION['user']->id,
                        'project' => $project,
                        'method' => $method,
                        'status' => '-1',               // aporte en proceso
                        'invested' => date('Y-m-d'),
                        'anonymous' => $_POST['anonymous'],
                        'resign' => $resign
                    )
                );
                if ($reward) {
                    $invest->rewards = array($chosen);
                }
                $invest->address = (object) $address;

                // saber si el aporte puede generar riego y cuanto
                if ($projectData->called instanceof Model\Call  && $projectData->called->dropable) {

                    // saber si este usuario ya ha generado riego
                    $allready = $projectData->called->getSupporters(true, $_SESSION['user']->id, $projectData->id);
                    if ($allready > 0) {
                        $invest->called = null;
                    } else  {
                        $invest->called = $projectData->called;
                        $invest->maxdrop = Model\Call\Project::currMaxdrop($projectData, $invest->amount);
                    }

                } else {
                    $invest->called = null;
                }

                if ($invest->save($errors)) {
                    Model\Invest::setDetail($invest->id, 'init', 'Se ha creado el registro de aporte, el usuario ha clickado el boton de tpv o paypal. Proceso controller/invest');

                    switch($method) {
                        case 'tpv':
                            // redireccion al tpv
                            if (Tpv::preapproval($invest, $errors)) {
                                die;
                            } else {
                                Message::Error(Text::get('invest-tpv-error_fatal'));
                            }
                            break;
                        case 'paypal':
                            // Petición de preapproval y redirección a paypal
                            if (Paypal::preapproval($invest, $errors)) {
                                die;
                            } else {
                                Message::Error(Text::get('invest-paypal-error_fatal'));
                            }
                            break;
                        case 'cash':
                            $invest->setStatus('1');
                            // En betatest aceptamos cash para pruebas
                            throw new Redirection("/invest/confirmed/{$project}/{$invest->id}");
                            break;
                    }
                } else {
                    Message::Error(Text::get('invest-create-error'));
                }
			} else {
                Message::Error(Text::get('invest-data-error'));
            }

            throw new Redirection("/project/$project/invest/?confirm=fail");
            //throw new Redirection("/project/$project/invest");
        }


        public function confirmed ($project = null, $invest = null) {
            if (empty($project) || empty($invest)) {
                Message::Error(Text::get('invest-data-error'));
                throw new Redirection('/discover', Redirection::TEMPORARY);
            }

            // para evitar las duplicaciones de feed y email
            if (isset($_SESSION['invest_'.$invest.'_completed'])) {
                Message::Info(Text::get('invest-process-completed'));
                throw new Redirection("/project/$project/invest/?confirm=ok");
            }

            // el aporte
            $confirm = Model\Invest::get($invest);
            $projectData = Model\Project::getMedium($project);

            // datos para el drop
            if (!empty($confirm->droped)) {
                $drop = Model\Invest::get($confirm->droped);
                $callData = Model\Call::getMini($drop->call);

                // texto de capital riego
                $txt_droped = Text::get('invest-mail_info-drop', $callData->user->name, $drop->amount, $callData->name);
            } else {
                $txt_droped = '';
            }


            // segun método
            /*----------------------------------
             * SOLAMENTE DESARROLLO Y PRUEBAS!!!
             -----------------------------------*/
            if ($confirm->method == 'cash') {
               
                // Evento Feed
                $log = new Feed();
                $log->setTarget($projectData->id);
                $log->populate('Aporte '.$confirm->method, '/admin/invests',
                    \vsprintf("%s ha aportado %s al proyecto %s mediante Cash", array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('money', $confirm->amount.' &euro;'),
                        Feed::item('project', $projectData->name, $projectData->id))
                    ));
                $log->doAdmin('money');
                // evento público
                $log->populate($_SESSION['user']->name, '/user/profile/'.$_SESSION['user']->id,
                    Text::html('feed-invest',
                                    Feed::item('money', $confirm->amount.' &euro;'),
                                    Feed::item('project', $projectData->name, $projectData->id)),
                    $_SESSION['user']->avatar->id);
                $log->doPublic('community');
                unset($log);
            }
            /*--------------------------------------
             * FIN SOLAMENTE DESARROLLO Y PRUEBAS!!!
             --------------------------------------*/

            if ($confirm->method == 'tpv') {
                // si el aporte no está en estado "cobrado por goteo" (1) 
                if ($confirm->status != '1') {
                    @mail('goteo_fail@doukeshi.org',
                        'Aporte tpv no pagado ' . $invest,
                        'Ha llegado a invest/confirm el aporte '.$invest.' mediante tpv sin estado cobrado (llega con estado '.$confirm->status.')');
                    // mandarlo a la pagina de aportar para que lo intente de nuevo
                    throw new Redirection("/project/$project/invest/?confirm=fail");
                }
            }

            // Paypal solo disponible si activado
            if ($confirm->method == 'paypal') {

                // hay que cambiarle el status a 0
                $confirm->setStatus('0');

                // Evento Feed
                $log = new Feed();
                $log->setTarget($projectData->id);
                $log->populate('Aporte PayPal', '/admin/invests',
                    \vsprintf("%s ha aportado %s al proyecto %s mediante PayPal",
                        array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('money', $confirm->amount.' &euro;'),
                        Feed::item('project', $projectData->name, $projectData->id))
                    ));
                $log->doAdmin('money');
                // evento público
                $log_html = Text::html('feed-invest',
                                    Feed::item('money', $confirm->amount.' &euro;'),
                                    Feed::item('project', $projectData->name, $projectData->id));
                if ($confirm->anonymous) {
                    $log->populate(Text::get('regular-anonymous'), '/user/profile/anonymous', $log_html, 1);
                } else {
                    $log->populate($_SESSION['user']->name, '/user/profile/'.$_SESSION['user']->id, $log_html, $_SESSION['user']->avatar->id);
                }
                $log->doPublic('community');
                unset($log);
            }
            // fin segun metodo

            // Feed del aporte de la campaña
            if (!empty($confirm->droped) && $drop instanceof Model\Invest && is_object($callData)) {
                // Evento Feed
                $log = new Feed();
                $log->setTarget($projectData->id);
                $log->populate('Aporte riego '.$drop->method, '/admin/invests',
                    \vsprintf("%s ha aportado %s de %s al proyecto %s a través de la campaña %s", array(
                        Feed::item('user', $callData->user->name, $callData->user->id),
                        Feed::item('money', $drop->amount.' &euro;'),
                        Feed::item('drop', 'Capital Riego', '/service/resources'),
                        Feed::item('project', $projectData->name, $projectData->id),
                        Feed::item('call', $callData->name, $callData->id)
                    )));
                $log->doAdmin('money');
                // evento público
                $log->populate($callData->user->name, '/user/profile/'.$callData->user->id,
                            Text::html('feed-invest',
                                    Feed::item('money', $drop->amount.' &euro;')
                                        . ' de '
                                        . Feed::item('drop', 'Capital Riego', '/service/resources'),
                                    Feed::item('project', $projectData->name, $projectData->id)
                                        . ' a través de la campaña '
                                        . Feed::item('call', $callData->name, $callData->id)
                            ), $callData->user->avatar->id);
                $log->doPublic('community');
                unset($log);
            }

            // texto recompensa
            $rewards = $confirm->rewards;
            array_walk($rewards, function (&$reward) { $reward = $reward->reward; });
            $txt_rewards = implode(', ', $rewards);

            // email de agradecimiento al cofinanciador
            // primero monto el texto de recompensas
            if ($confirm->resign) {
                // Plantilla de donativo segun la ronda
                if ($projectData->round == 2) {
                    $template = Template::get(36); // en segunda ronda
                } else {
                    $template = Template::get(28); // en primera ronda
                }
            } else {
                // plantilla de agradecimiento segun la ronda
                if ($projectData->round == 2) {
                    $template = Template::get(34); // en segunda ronda
                } else {
                    $template = Template::get(10); // en primera ronda
                }
            }

            $URL = (NODE_ID != GOTEO_NODE) ? NODE_URL : SITE_URL;
            
            // Dirección en el mail
            $txt_address = Text::get('invest-mail_info-address');
            $txt_address .= '<br> ' . Text::get('invest-address-address-field') . ' ' . $confirm->address->address;
            $txt_address .= '<br> ' . Text::get('invest-address-zipcode-field') . ' ' . $confirm->address->zipcode;
            $txt_address .= '<br> ' . Text::get('invest-address-location-field') . ' ' . $confirm->address->location;
            $txt_address .= '<br> ' . Text::get('invest-address-country-field') . ' ' . $confirm->address->country;

            // Agradecimiento al cofinanciador
            // Sustituimos los datos
            $subject = str_replace('%PROJECTNAME%', $projectData->name, $template->title);

            // En el contenido:
            $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%', '%REWARDS%', '%ADDRESS%', '%DROPED%');
            $replace = array($_SESSION['user']->name, $projectData->name, $URL.'/project/'.$projectData->id, $confirm->amount, $txt_rewards, $txt_address, $txt_droped);
            $content = \str_replace($search, $replace, $template->text);

            $mailHandler = new Mail();
            $mailHandler->from = GOTEO_CONTACT_MAIL;
            $mailHandler->to = $_SESSION['user']->email;
            $mailHandler->toName = $_SESSION['user']->name;
            $mailHandler->subject = $subject;
            $mailHandler->content = $content;
            $mailHandler->html = true;
            $mailHandler->template = $template->id;
            if ($mailHandler->send($errors)) {
                Message::Info(Text::get('project-invest-thanks_mail-success'));
            } else {
                Message::Error(Text::get('project-invest-thanks_mail-fail'));
                Message::Error(implode('<br />', $errors));
            }

            unset($mailHandler);
            

            // Notificación al autor
            $template = Template::get(29);
            // Sustituimos los datos
            $subject = str_replace('%PROJECTNAME%', $projectData->name, $template->title);

            // En el contenido:
            $search  = array('%OWNERNAME%', '%USERNAME%', '%PROJECTNAME%', '%SITEURL%', '%AMOUNT%', '%MESSAGEURL%', '%DROPED%');
            $replace = array($projectData->user->name, $_SESSION['user']->name, $projectData->name, $URL, $confirm->amount, $URL.'/user/profile/'.$_SESSION['user']->id.'/message', $txt_droped);
            $content = \str_replace($search, $replace, $template->text);

            $mailHandler = new Mail();

            $mailHandler->to = $projectData->user->email;
            $mailHandler->toName = $projectData->user->name;
            $mailHandler->subject = $subject;
            $mailHandler->content = $content;
            $mailHandler->html = true;
            $mailHandler->template = $template->id;
            $mailHandler->send();

            unset($mailHandler);

            // marcar que ya se ha completado el proceso de aportar
            $_SESSION['invest_'.$invest.'_completed'] = true;

            // log
            Model\Invest::setDetail($invest, 'confirmed', 'El usuario regresó a /invest/confirmed');
            
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

            // dejamos el aporte tal cual esta
            Model\Invest::setDetail($id, 'confirm-fail', 'El usuario regresó a /invest/fail');

            // mandarlo a la pagina de aportar para que lo intente de nuevo
            throw new Redirection("/project/$project/invest/?confirm=fail", Redirection::TEMPORARY);
        }

        // resultado del cargo
        public function charge ($result = null, $id = null) {
            if (empty($id) || !\in_array($result, array('fail', 'success'))) {
                die;
            }
            // de cualquier manera no hacemos nada porque esto no lo ve ningun usuario
            die;
        }


    }

}