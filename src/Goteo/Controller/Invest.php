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
        Goteo\Model\Template,
        Goteo\Application\Message,
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
                Message::error(Text::get('investing_closed'));
                throw new Redirection('/project/'.$project);
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $errors = array();
                $method = \strtolower($_POST['method']);

                if ($debug) echo \trace($_POST);

                $_amount = $_POST['amount'];
                if (empty($_amount)) {
                    Message::error(Text::get('invest-amount-error'));
                    throw new Redirection(SEC_URL."/project/$project/invest?confirm=fail", Redirection::TEMPORARY);
                }

                // conversión a euros
                if ($_SESSION['currency'] != Currency::DEFAULT_CURRENCY) {
                    $rate = Currency::rate();
                } else {
                    $rate = 1;
                }
                $amount =  round($_amount / $rate);
                if ($debug) var_dump("$_amount / $rate = $amount  from aprox ".($_amount / $rate) );
                $amount = \number_format($amount, 0, '', '');

                // si está marcado "a reservar" llega $_POST['pool']

                // Funcionalidad crédito:
                $pool = Model\User\Pool::get($user->id);

                // si el usuario tiene gotas el metodo 'pool' es permitido
                if ($pool->amount > 0) {
                    $methods['pool'] = 'pool';
                    if ($pool->amount <  $amount && $method == "pool" ) {
                        // pero no son suficientes para este amount, error
                        Message::Error(Text::get('invest-pool-error'));
                        throw new Redirection(SEC_URL."/project/$project/invest?confirm=fail", Redirection::TEMPORARY);
                    }
                }

                if (!isset($methods[$method])) {
                    Message::Error(Text::get('invest-method-error'));
                    throw new Redirection(SEC_URL."/project/$project/invest?confirm=fail", Redirection::TEMPORARY);
                }


                // si es a reservar
                $to_pool = $_POST['pool'];

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
                    Message::error(Text::get('invest-owner-error'));
                    throw new Redirection(SEC_URL."/project/$project/invest?confirm=fail", Redirection::TEMPORARY);
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

                $invest = new Model\Invest(
                    array(
                        'amount' => $amount,
                        'amount_original' => $_amount,
                        'currency' => $_SESSION['currency'],
                        'currency_rate' => $rate,
                        'user' => $user->id,
                        'project' => $project,
                        'method' => $method,
                        'status' => Model\Invest::STATUS_PROCESSING,  // aporte en proceso
                        'invested' => date('Y-m-d'),
                        'anonymous' => $_POST['anonymous'],
                        'resign' => $resign,
                        'pool' => $to_pool
                    )
                );

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
                    }

                } else {
                    $invest->called = null;
                }

               // if ($debug) die(\trace($invest));

                if ($invest->save($errors)) {
                    // urls para paypal (necesita schema)
                    if (substr(SITE_URL, 0, 2) == '//') {
                        $URL = (\GOTEO_ENV != 'real') ? 'http:'.SITE_URL : 'https:'.SITE_URL;
                    } else {
                        $URL = SITE_URL;
                    }

                    $invest->urlOK  = $URL."/invest/confirmed/{$project}/{$invest->id}";
                    $invest->urlNOK = $URL."/invest/fail/{$project}/{$invest->id}";

                    Model\Invest::setDetail($invest->id, 'init', 'Se ha creado el registro de aporte, el usuario ha clickado el boton de tpv o paypal. Proceso controller/invest');

                    switch($method) {
                        case 'tpv':
                            // redireccion al tpv
                            if (Tpv::preapproval($invest, $errors)) {
                                die;
                            } else {
                                Message::error(Text::get('invest-tpv-error_fatal'));
                            }
                            break;
                        case 'paypal':
                            // si es un aporte a reservar se paga con expresscheckout (y pronto siempre así)
                            if ($invest->pool) {
                                // expresscheckout
                                // Petición de token y redirección a paypal
                                if (Paypal::preparePay($invest, $errors)) {
                                    die;
                                } else {
                                    Message::error(Text::get('invest-paypal-error_fatal'));
                                }
                            } else {

                                // Petición de preapproval y redirección a paypal
                                if (Paypal::preapproval($invest, $errors)) {
                                    die;
                                } else {
                                    Message::error(Text::get('invest-paypal-error_fatal'));
                                }

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
                                } else {
                                    Message::error(implode('<br />, $errors)'));
                                }
                            break;
                    }
                } else {
                    Message::error(Text::get('invest-create-error').'<br />'.implode('<br />, $errors)'));
                }
			} else {
                Message::error(Text::get('invest-data-error'));
            }

            throw new Redirection("/project/$project/invest?confirm=fail");
        }

        /* para atender url de confirmación de aporte
         * @params project id del proyecto ('bazargoteo' para hacerlo volver al catálogo)
         * @params id id del aporte
         * @params reward recompensa que selecciona
         */
        public function confirmed ($project = null, $id = null, $reward = null) {
            if (empty($id)) {
                Message::error(Text::get('invest-data-error'));
                throw new Redirection('/', Redirection::TEMPORARY);
            }

            // el usuario
            $user = Session::getUser();

            // el aporte
            $invest = Model\Invest::get($id);

            $projectData = Model\Project::getMedium($invest->project);

            // si es de Bazar, a /bazar/id-reward/thanks
            $retUrl = ($project == 'bazargoteo') ? "/bazaar/{$reward}/thanks" : "/project/{$invest->project}/invest?confirm=ok";

            // para evitar las duplicaciones de feed y email
            if (isset($_SESSION['invest_'.$invest->id.'_completed'])) {
                Message::info(Text::get('invest-process-completed'));
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
                        throw new Redirection("/project/{$invest->project}/invest?confirm=fail");
                }
            }
            */

            // Paypal solo disponible si activado
            if ($invest->method == 'paypal') {

                if (!empty($invest->preapproval)) {

                    // si es preapproval hay que cambiarle el status a 0 (preapprovado)
                    $invest->setStatus(Model\Invest::STATUS_PENDING);

                } elseif (isset($_GET['token']) && $_GET['token'] == $invest->payment) {

                    // retorno valido
                    $token = $_GET['token'];
                    $payerid = $_GET['PayerID'];

                    $invest->setAccount($payerid);
                    $invest->account = $payerid;

                    // completamos con el DoEsxpresscheckout despues de comprobar que está completado y cobrado
                    if (Paypal::completePay($invest, $errors)) {
                        // ok
                        Model\Invest::setDetail($invest->id, 'paypal-completed', 'El usuario ha regresado de PayPal y recibimos el token: '.$token.'  y el PayerID '.$payerid.'.');

                    } else {
                        Model\Invest::setDetail($invest->id, 'paypal-completion-error', 'El usuario ha regresado de PayPal y recibimos el token: '.$token.'  y el PayerID '.$payerid.'. Pero completePay ha fallado. <pre>'.print_r($invest ,1).'</pre>');
                        throw new Redirection("/project/$project/invest?confirm=fail");
                    }

                } else {
                    Model\Invest::setDetail($invest->id, 'paypal-return-error', 'El usuario ha regresado de un aporte de PayPal pero no tiene ni preapproval ni token. <pre>'.print_r($invest ,1).'</pre>');
                    throw new Redirection("/project/$project/invest?confirm=fail");
                }


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

            // Feed al aportar usndo gotas
            if ($invest->method == 'pool') {
                // Evento Feed
                $log = new Feed();
                $log->setTarget($projectData->id);
                $log->populate('Aporte Monedero', '/admin/invests',
                    \vsprintf("%s ha aportado %s al proyecto %s mediante Monedero",
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


            // Agradecimiento al cofinanciador

            //  idioma de preferencia
            $prefer = Model\User::getPreferences($user->id);
            $comlang = !empty($prefer->comlang) ? $prefer->comlang : $user->lang;


            // plantilla agradecimiento
            $template = Template::get(64, $comlang);

            // activamos idioma comunicaciones para los textos
            $_SESSION['VAR_LANG'] = $comlang;

            // primero monto el texto de recompensas (o renuncia)
            if($invest->resign){
                $txt_rewards = Text::get('invest-template-resign');
            } else {
                $txt_rewards = str_replace('%REWARDS%', $txt_rewards, Text::get('invest-template-reward'));
            }

            // aporte usando gotas:
            if ($invest->method == 'pool') {
                $txt_method = str_replace('%AMOUNT%', \amount_format($invest->amount), Text::get('invest-template-with-pool'));
            } elseif ($invest->pool) {
                // aporte reservando al monedero
                $txt_method = str_replace('%AMOUNT%', \amount_format($invest->amount), Text::get('invest-template-to-pool'));
            } elseif($projectData->round == 2) {
                // si aporte en segunda ronda
                $txt_method = str_replace('%AMOUNT%', $invest->amount, Text::get('invest-template-round-two'));
            } else {
                // resto de casos
                $txt_method = str_replace('%AMOUNT%', $invest->amount, Text::get('invest-template-round-one'));
            }

            $URL = \SITE_URL;

            // Dirección en el mail (y version para regalo)
            $txt_address = Text::get('invest-address-address-field') . ' ' . $invest->address->address;
            $txt_address .= '<br> ' . Text::get('invest-address-zipcode-field') . ' ' . $invest->address->zipcode;
            $txt_address .= '<br> ' . Text::get('invest-address-location-field') . ' ' . $invest->address->location;
            $txt_address .= '<br> ' . Text::get('invest-address-country-field') . ' ' . $invest->address->country;

            $txt_destaddr = $txt_address;
            $txt_address = Text::get('invest-mail_info-address') .'<br>'. $txt_address;

            // desactivamos idioma comunicaciones para textos
            unset($_SESSION['VAR_LANG']);


            // Sustituimos los datos
            $subject = str_replace('%PROJECTNAME%', $projectData->name, $template->title);

            // En el contenido:
            $search  = array('%USERNAME%', '%PROJECTNAME%', '%PROJECTURL%', '%AMOUNT%', '%REWARDS%', '%ADDRESS%', '%DROPED%', '%METHOD%');
            $replace = array($user->name, $projectData->name, $URL.'/project/'.$projectData->id, $invest->amount, $txt_rewards, $txt_address, $txt_droped, $txt_method);
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
                Message::info(Text::get('project-invest-thanks_mail-success'));
            } else {
                Message::error(Text::get('project-invest-thanks_mail-fail'));
                Message::error(implode('<br />', $errors));
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
                    Message::info(Text::get('project-invest-friend_mail-success'));
                } else {
                    Message::error(Text::get('project-invest-friend_mail-fail'));
                    Message::error(implode('<br />', $errors));
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
                throw new Redirection("/project/{$project}/invest?confirm=fail");
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
