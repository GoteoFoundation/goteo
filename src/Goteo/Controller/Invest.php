<?php

namespace Goteo\Controller {

    use Goteo\Application\Session;
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
        Goteo\Library\Tpv,
        Goteo\Library\Currency;

    class Invest extends \Goteo\Core\Controller {

        // metodos habilitados
        public static $methods = array(
                'tpv' => 'tpv',
                'paypal' => 'paypal'
            );

        /*
         *  Este controlador no sirve ninguna página
         */
        public function index ($project = null) {

            $user = Session::getUser();

            $debug = ($user->id == 'root');

            if (empty($project))
                throw new Redirection('/discover', Redirection::TEMPORARY);

            $message = '';

            $projectData = Model\Project::get($project);
            $methods = self::$methods;

            if (\GOTEO_ENV  != 'real' || $user->id == 'root') {
                $methods['cash'] = 'cash';
            }

            // si no está en campaña no pueden esta qui ni de coña
            if ($projectData->status != 3) {
                throw new Redirection('/project/'.$project, Redirection::TEMPORARY);
            }

            if ($projectData->noinvest) {
                Message::Error(Text::get('investing_closed'));
                throw new Redirection('/project/'.$project);
            }

            // Funcionalidad crédito:
            // si el usuario tiene gotas el metodo 'pool' es permitido
            // si el usuario tiene gotas, pero no son suficientes para este amount, error


            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $errors = array();
                $los_datos = $_POST;
                $method = \strtolower($_POST['method']);

                if (!isset($methods[$method])) {
                    Message::Error(Text::get('invest-method-error'));
                    throw new Redirection(SEC_URL."/project/$project/invest/?confirm=fail", Redirection::TEMPORARY);
                }

                if (empty($_POST['amount'])) {
                    Message::Error(Text::get('invest-amount-error'));
                    throw new Redirection(SEC_URL."/project/$project/invest/?confirm=fail", Redirection::TEMPORARY);
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

                if ($projectData->owner == $user->id) {
                    Message::Error(Text::get('invest-owner-error'));
                    throw new Redirection(SEC_URL."/project/$project/invest/?confirm=fail", Redirection::TEMPORARY);
                }

                // añadir recompensas que ha elegido
                $chosen = $_POST['selected_reward'];
                if ($chosen == 0) {
                    // renuncia a las recompensas, bien por el/ella
                    $resign = true;
                    $reward = false;
                } else {
                    // ya no se aplica esto de recompensa es de tipo Reconocimiento para donativo
                    $resign = false;
                    $reward = true;
                }

                // insertamos los datos personales del usuario si no tiene registro aun
                Model\User::setPersonal($user->id, $address, false);

                if ($debug) echo \trace($_POST);
                // conversión a euros
                if ($_SESSION['currency'] != Currency::DEFAULT_CURRENCY) {
                    $rate = Currency::rate();
                } else {
                    $rate = 1;
                }
                $amount_original = $_POST['amount'];
                $amount =  round($amount_original / $rate);
                if ($debug) var_dump("$amount_original / $rate = $amount  from aprox ".($amount_original / $rate) );
                $amount = \number_format($amount, 0, '', '');

                // si está marcado "a reservar" llega $_POST['pool']

                $invest = new Model\Invest(
                    array(
                        'amount' => $amount,
                        'amount_original' => $amount_original,
                        'currency' => $_SESSION['currency'],
                        'currency_rate' => $rate,
                        'user' => $user->id,
                        'project' => $project,
                        'method' => $method,
                        'status' => '-1',               // aporte en proceso
                        'invested' => date('Y-m-d'),
                        'anonymous' => $_POST['anonymous'],
                        'resign' => $resign,
                        'pool' => $_POST['pool']
                    )
                );

                if ($debug) die(\trace($invest));

                if ($reward) {
                    $invest->rewards = array($chosen);
                }
                $invest->address = (object) $address;

                // saber si el aporte puede generar riego y cuanto
                if ($projectData->called instanceof Model\Call  && $projectData->called->dropable) {

                    // saber si este usuario ya ha generado riego
                    $allready = $projectData->called->getSupporters(true, $user->id, $projectData->id);
                    if ($allready > 0) {
                        $invest->called = null;
                    } else  {
                        $invest->called = $projectData->called;
                        $invest->maxdrop = Model\Call\Project::setMaxdrop($projectData, $invest->amount);
                    }

                } else {
                    $invest->called = null;
                }

                if ($invest->save($errors)) {
                    $invest->urlOK  = SEC_URL."/invest/confirmed/{$project}/{$invest->id}";
                    $invest->urlNOK = SEC_URL."/invest/fail/{$project}/{$invest->id}";
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
                            // En betatest aceptamos cash para pruebas
                            if (GOTEO_ENV != 'real') {
                                $invest->setStatus('1');
                                throw new Redirection($invest->urlOK);
                            } else {
                                throw new Redirection('/');
                            }
                            break;
                        case 'pool':
                            // gastar de la reserva y redirect a ok
                                Model\User\Pool::withdraw($user->id, $invest->amount, $errors);
                                if (empty($errors)) {
                                    $invest->setStatus('1');
                                    throw new Redirection($invest->urlOK);
                                }
                            break;
                    }
                } else {
                    Message::Error(Text::get('invest-create-error').'<br />'.implode('<br />, $errors)'));
                }
			} else {
                Message::Error(Text::get('invest-data-error'));
            }

            throw new Redirection("/project/$project/invest/?confirm=fail");
        }

        /* para atender url de confirmación de aporte
         * @params project id del proyecto ('bazargoteo' para hacerlo volver al catálogo)
         * @params id id del aporte
         * @params reward recompensa que selecciona
         */
        public function confirmed ($project = null, $id = null, $reward = null) {
            if (empty($id)) {
                Message::Error(Text::get('invest-data-error'));
                throw new Redirection('/', Redirection::TEMPORARY);
            }

            // el usuario
            $user = Session::getUser();

            // el aporte
            $invest = Model\Invest::get($id);

            $projectData = Model\Project::getMedium($invest->project);

            // si es de Bazar, a /bazar/id-reward/thanks
            $retUrl = ($project == 'bazargoteo') ? "/bazaar/{$reward}/thanks" : "/project/{$invest->project}/invest/?confirm=ok";

            // para evitar las duplicaciones de feed y email
            if (isset($_SESSION['invest_'.$invest->id.'_completed'])) {
                Message::Info(Text::get('invest-process-completed'));
                throw new Redirection($retUrl);
            }


            // datos para el drop
            if (!empty($invest->droped)) {
                $drop = Model\Invest::get($invest->droped);
                $callData = Model\Call::getMini($drop->call);

                // texto de capital riego
                $txt_droped = Text::get('invest-mail_info-drop', $callData->user->name, $drop->amount, $callData->name);
            } else {
                $txt_droped = '';
            }


            // segun método
            /*
            // esto es posible porque el cambio de estado se hace en la comunicación online
            if ($invest->method == 'tpv') {
                // si el aporte no está en estado "cobrado por goteo" (1)
                if ($invest->status != '1') {
                    @mail(\GOTEO_FAIL_MAIL,
                        'Aporte tpv no pagado ' . $invest->id,
                        'Ha llegado a invest/confirm el aporte '.$invest->id.' mediante tpv sin estado cobrado (llega con estado '.$invest->status.')');
                    // mandarlo a la pagina de aportar para que lo intente de nuevo
                    // si es de Bazar, a la del producto del catálogo
                    if ($project == 'bazargoteo')
                        throw new Redirection("/bazaar/{$reward}/fail");
                    else
                        throw new Redirection("/project/{$invest->project}/invest/?confirm=fail");
                }
            }
            */

            // Paypal solo disponible si activado
            if ($invest->method == 'paypal') {

                // hay que cambiarle el status a 0
                $invest->setStatus('0');

                // Evento Feed
                $log = new Feed();
                $log->setTarget($projectData->id);
                $log->populate('Aporte PayPal', '/admin/invests',
                    \vsprintf("%s ha aportado %s al proyecto %s mediante PayPal",
                        array(
                        Feed::item('user', $user->name, $user->id),
                        Feed::item('money', $invest->amount.' &euro;'),
                        Feed::item('project', $projectData->name, $projectData->id))
                    ));
                $log->doAdmin('money');
                // evento público
                $log_html = Text::html('feed-invest',
                                    Feed::item('money', $invest->amount.' &euro;'),
                                    Feed::item('project', $projectData->name, $projectData->id));
                if ($invest->anonymous) {
                    $log->populate(Text::get('regular-anonymous'), '/user/profile/anonymous', $log_html, 1);
                } else {
                    $log->populate($user->name, '/user/profile/'.$user->id, $log_html, $user->avatar->id);
                }
                $log->doPublic('community');
                unset($log);
            }
            // fin segun metodo

            // Feed del aporte de la campaña
            if (!empty($invest->droped) && $drop instanceof Model\Invest && is_object($callData)) {
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

            // recalculo
            $invest->keepUpdated($callData->id);

            // texto recompensa
            // @TODO quitar esta lacra de N recompensas porque ya es solo una recompensa siempre
            $rewards = $invest->rewards;
            array_walk($rewards, function (&$reward) { $reward = $reward->reward; });
            $txt_rewards = implode(', ', $rewards);

            // recaudado y porcentaje
            $amount = $projectData->amount;
            $percent = floor(($projectData->amount / $projectData->mincost) * 100);


            // email de agradecimiento al cofinanciador

            //  idioma de preferencia
            $prefer = Model\User::getPreferences($user->id);
            $comlang = !empty($prefer->comlang) ? $prefer->comlang : $user->lang;

            // primero monto el texto de recompensas
            // @FIXME : estas  4 plantillas tendrian que ser una sola, con textos dinamicos según si renuncia y primera/segunda ronda
            if ($invest->resign) {
                // Plantilla de donativo segun la ronda
                if ($projectData->round == 2) {
                    $template = Template::get(36, $comlang); // en segunda ronda
                } else {
                    $template = Template::get(28, $comlang); // en primera ronda
                }
            } else {
                // plantilla de agradecimiento segun la ronda
                if ($projectData->round == 2) {
                    $template = Template::get(34, $comlang); // en segunda ronda
                } else {
                    $template = Template::get(10, $comlang); // en primera ronda
                }
            }

            $URL = \SITE_URL;

            // Dirección en el mail (y version para regalo)
            $txt_address = Text::get('invest-address-address-field') . ' ' . $invest->address->address;
            $txt_address .= '<br> ' . Text::get('invest-address-zipcode-field') . ' ' . $invest->address->zipcode;
            $txt_address .= '<br> ' . Text::get('invest-address-location-field') . ' ' . $invest->address->location;
            $txt_address .= '<br> ' . Text::get('invest-address-country-field') . ' ' . $invest->address->country;

            $txt_destaddr = $txt_address;
            $txt_address = Text::get('invest-mail_info-address') .'<br>'. $txt_address;

            // Agradecimiento al cofinanciador
            // Sustituimos los datos
            $subject = str_replace('%PROJECTNAME%', $projectData->name, $template->title);

            // En el contenido:
            $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%', '%REWARDS%', '%ADDRESS%', '%DROPED%');
            $replace = array($user->name, $projectData->name, $URL.'/project/'.$projectData->id, $invest->amount, $txt_rewards, $txt_address, $txt_droped);
            $content = \str_replace($search, $replace, $template->text);

            $mailHandler = new Mail();
            $mailHandler->reply = GOTEO_CONTACT_MAIL;
            $mailHandler->replyName = GOTEO_MAIL_NAME;
            $mailHandler->to = $user->email;
            $mailHandler->toName = $user->name;
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

            // si es un regalo
            if ($invest->address->regalo && !empty($invest->address->emaildest)) {
                // Notificación al destinatario de regalo
                $template = Template::get(53);
                // Sustituimos los datos
                $subject = str_replace('%USERNAME%', $user->name, $template->title);

                // En el contenido:
                $search  = array('%DESTNAME%', '%USERNAME%', '%MESSAGE%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%', '%PROJAMOUNT%', '%PROJPER%', '%REWNAME%', '%ADDRESS%', '%DROPED%');
                $replace = array($invest->address->namedest, $user->name, $invest->address->message, $projectData->name, $URL.'/project/'.$projectData->id, $invest->amount, $amount, $percent, $txt_rewards, $txt_destaddr, $txt_droped);
                $content = \str_replace($search, $replace, $template->text);

                $mailHandler = new Mail();

                $mailHandler->to = $invest->address->emaildest;
                $mailHandler->toName = $invest->address->namedest;
                $mailHandler->subject = $subject;
                $mailHandler->content = $content;
                $mailHandler->html = true;
                $mailHandler->template = $template->id;
                if ($mailHandler->send($errors)) {
                    Message::Info(Text::get('project-invest-friend_mail-success'));
                } else {
                    Message::Error(Text::get('project-invest-friend_mail-fail'));
                    Message::Error(implode('<br />', $errors));
                }

                unset($mailHandler);
            }


            // Notificación al autor

            //  idioma de preferencia
            $prefer = Model\User::getPreferences($projectData->user->id);
            $comlang = !empty($prefer->comlang) ? $prefer->comlang : $projectData->user->lang;

            $template = Template::get(29, $comlang);
            // Sustituimos los datos
            $subject = str_replace('%PROJECTNAME%', $projectData->name, $template->title);

            // En el contenido:
            $search  = array('%OWNERNAME%', '%USERNAME%', '%PROJECTNAME%', '%SITEURL%', '%AMOUNT%', '%MESSAGEURL%', '%DROPED%');
            $replace = array($projectData->user->name, $user->name, $projectData->name, $URL, $invest->amount, $URL.'/user/profile/'.$user->id.'/message', $txt_droped);
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
            $_SESSION['invest_'.$invest->id.'_completed'] = true;

            // log
            Model\Invest::setDetail($invest->id, 'confirmed', 'El usuario regresó a /invest/confirmed');

            // mandarlo a la pagina de gracias
            throw new Redirection($retUrl);
        }

        /*
         * Para servir la urlNOK
         * @params project id del proyecto ('bazargoteo' para hacerlo volver al catálogo)
         * @params id id del aporte
         * @params reward recompensa que selecciona
         */
        public function fail ($project = null, $id = null, $reward = null) {
            if (empty($project))
                throw new Redirection('/');

            if (empty($id)) {
                if ($project == 'bazargoteo')
                    throw new Redirection("/bazaar");
                else
                    throw new Redirection("/project/{$project}/invest");
            }

            // dejamos el aporte tal cual esta
            Model\Invest::setDetail($id, 'confirm-fail', 'El usuario regresó a /invest/fail');

            // mandarlo a la pagina de aportar para que lo intente de nuevo
            //@TODO-Bazar redirección al bazar, sin instancias habrá que inventarse algo
            if ($project == 'bazargoteo')
                throw new Redirection("/bazaar/{$reward}/fail");
            else
                throw new Redirection("/project/{$project}/invest/?confirm=fail");
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
