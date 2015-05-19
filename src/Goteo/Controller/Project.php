<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Application\Session,
        Goteo\Controller\Cron\Send,
        Goteo\Library\Text,
        Goteo\Library\Check,
        Goteo\Library,
        Goteo\Library\Template,
        Goteo\Library\Feed,
        Goteo\Model;

    class Project extends \Goteo\Core\Controller {

        public function index($id = null, $show = 'home', $post = null) {
            if ($id !== null) {
                return $this->view($id, $show, $post);
            } else if (isset($_GET['create'])) {
                throw new Redirection("/project/create");
            } else {
                throw new Redirection("/discover");
            }
        }

        public function raw ($id) {
            $project = Model\Project::get($id, LANG);
            // pasos para el check
            if ($project->draft) {
                // primer borrador, menos pasos
                $steps = array('userProfile', 'overview', 'costs', 'rewards');
            } else {
                // todos los pasos
                $steps = array('userProfile', 'userPersonal', 'overview', 'images', 'costs', 'rewards', 'supports');
            }
            $project->check($steps);
            \trace($project->called);
            \trace($project);
            die;
        }

        public function delete ($id) {
            $user = Session::getUser();
            // redirección según usuario
            $goto = isset($user->roles['admin']) ? '/admin/projects' : '/dashboard/projects';

            // preveer posible cambio de id
            try {
                $project = Model\Project::getMini($id, null);

            } catch(\Goteo\Core\Error $e) {
                throw new Redirection('/dashboard/projects');
            }

            // no lo puede eliminar si
            if (!Model\Project::userRemovable($project, Session::getUser())) {
                Library\Message::Info('No tienes permiso para eliminar este proyecto');
                throw new Redirection($goto);
            }

            $errors = array();
            if ($project->delete($errors)) {
                Library\Message::Info("Has borrado los datos del proyecto '<strong>{$project->name}</strong>' correctamente");
                if ($_SESSION['project']->id == $id) {
                    unset($_SESSION['project']);
                }
            } else {
                Library\Message::Info("No se han podido borrar los datos del proyecto '<strong>{$project->name}</strong>'. Error:" . implode(', ', $errors));
            }
            throw new Redirection($goto);
        }

        //Aunque no esté en estado edición un admin siempre podrá editar un proyecto
        public function edit ($id, $step = 'userProfile') {
            $user = Session::getUser();
            // redirección según usuario
            $goto = isset($user->roles['admin']) ? '/admin/projects' : '/dashboard/projects';

            // preveer posible cambio de id
            try {
                $project = Model\Project::get($id, null);

            } catch(\Goteo\Core\Error $e) {
                throw new Redirection('/dashboard/projects');
            }

            if (!Model\Project::userEditable($project, Session::getUser())) {
                Library\Message::Info('No tienes permiso para editar este proyecto');
                throw new Redirection($goto);
            }

            $currency_data = Library\Currency::$currencies[$project->currency];

            // al impulsor se le prohibe ver ningun paso cuando ya no está en edición
            if ($project->status != 1 && $project->owner == $user->id ) {
                // solo puede estar en preview
                $step = 'preview';

                $steps = array(
                    'preview' => array(
                        'name' => Text::get('step-7'),
                        'title' => Text::get('step-preview'),
                        'offtopic' => true
                    )
                );


            } elseif ($project->draft) {
                // primer borrador, menos pasos
                $steps = array(
                    'userProfile' => array(
                        'name' => Text::get('step-1'),
                        'title' => Text::get('step-userProfile'),
                        'offtopic' => true
                    ),
                    'overview' => array(
                        'name' => Text::get('step-3'),
                        'title' => Text::get('step-overview')
                    ),
                    'costs'=> array(
                        'name' => Text::get('step-4'),
                        'title' => Text::get('step-costs')
                    ),
                    'rewards' => array(
                        'name' => Text::get('step-5'),
                        'title' => Text::get('step-rewards')
                    ),
                    'preview' => array(
                        'name' => Text::get('step-7'),
                        'title' => Text::get('step-preview'),
                        'offtopic' => true
                    )
                );

            } else {
                // todos los pasos
                // entrando, por defecto, en el paso especificado en url
                $steps = array(
                    'userProfile' => array(
                        'name' => Text::get('step-1'),
                        'title' => Text::get('step-userProfile'),
                        'offtopic' => true
                    ),
                    'userPersonal' => array(
                        'name' => Text::get('step-2'),
                        'title' => Text::get('step-userPersonal'),
                        'offtopic' => true
                    ),
                    'overview' => array(
                        'name' => Text::get('step-3'),
                        'title' => Text::get('step-overview')
                    ),
                    'images' => array(
                        'name' => Text::get('step-3b'),
                        'title' => Text::get('step-images')
                    ),
                    'costs'=> array(
                        'name' => Text::get('step-4'),
                        'title' => Text::get('step-costs')
                    ),
                    'rewards' => array(
                        'name' => Text::get('step-5'),
                        'title' => Text::get('step-rewards')
                    ),
                    'supports' => array(
                        'name' => Text::get('step-6'),
                        'title' => Text::get('step-supports')
                    ),
                    'preview' => array(
                        'name' => Text::get('step-7'),
                        'title' => Text::get('step-preview'),
                        'offtopic' => true
                    )
                );
            }



            foreach ($_REQUEST as $k => $v) {
                if (strncmp($k, 'view-step-', 10) === 0 && !empty($v) && !empty($steps[substr($k, 10)])) {
                    $step = substr($k, 10);
                }
            }

            if ($step == 'images') {
                // para que tenga todas las imágenes al procesar el post
                $project->images = Model\Image::getAll($id, 'project');
                $fragment = '#images';
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {

               // @DEBUG  die( \trace($_POST) );

                $errors = array(); // errores al procesar, no son errores en los datos del proyecto

                foreach ($steps as $id => &$data) {

                    if (call_user_func_array(array($this, "process_{$id}"), array(&$project, &$errors))) {
                        // Ok...
                    }

                }

                // guardamos los datos que hemos tratado y los errores de los datos
                $project->save($errors);

                // hay que mostrar errores en la imagen
                if (!empty($errors['image'])) {
                    Library\Message::Error(is_array($errors['image']) ? implode('<br />', $errors['image']) : $errors['image']);
                }


                // en los pasos de listas de items (webs, costes, retornos, colaboracioens)
                foreach ($_POST as $k => $v) {

                    // webs
                    if (!empty($v) && preg_match('/web-(\d+)-edit/', $k, $r)) {
                        $_SESSION['superform_item_edit'] = $k;
                        $fragment = "#$k";
                        break;
                    }


                    // costes
                    if (!empty($v) && preg_match('/cost-(\d+)-edit/', $k, $r)) {
                        $_SESSION['superform_item_edit'] = $k;
                        $fragment = "#$k";
                        break;
                    }


                    // recompensa
                    if (!empty($v) && preg_match('/((social)|(individual))_reward-(\d+)-edit/', $k)) {
                        $_SESSION['superform_item_edit'] = $k;
                        $fragment = "#$k";
                        break;
                    }


                    // colaboraciones
                    if (!empty($v) && preg_match('/support-(\d+)-edit/', $k, $r)) {
                        $_SESSION['superform_item_edit'] = $k;
                        $fragment = "#$k";
                        break;
                    }

                }

                // nueva web
                if (isset($_POST['web-add'])) {
                    $last = end($project->user->webs);
                    if ($last !== false) {
                        $k = "web-{$last->id}-edit";
                        $_SESSION['superform_item_edit'] = $k;
                        // $viewData["web-{$last->id}-edit"] = true;
                        //$fragment = "#$k";
                        $fragment = "#webs";
                    }
                }

                // nuevo coste
                if (isset($_POST['cost-add'])) {
                    $last = end($project->costs);
                    if ($last !== false) {
                        $k = "cost-{$last->id}-edit";
                        $_SESSION['superform_item_edit'] = $k;
                        // $viewData["cost-{$last->id}-edit"] = true;
                        $fragment = "#$k";
                    }
                }

                // nuevo retorno / recompensa
                if (!empty($_POST['social_reward-add'])) {
                    $last = end($project->social_rewards);
                    if ($last !== false) {
                        $k = "social_reward-{$last->id}-edit";
                        $_SESSION['superform_item_edit'] = $k;
                        // $viewData["social_reward-{$last->id}-edit"] = true;
                        $fragment = "#$k";
                    }
                }

                if (!empty($_POST['individual_reward-add'])) {
                    $last = end($project->individual_rewards);
                    if ($last !== false) {
                        $k = "individual_reward-{$last->id}-edit";
                        $_SESSION['superform_item_edit'] = $k;
                        // $viewData["individual_reward-{$last->id}-edit"] = true;
                        $fragment = "#$k";
                    }
                }

                // nueva colaboración
                if (!empty($_POST['support-add'])) {
                    $last = end($project->supports);
                    if ($last !== false) {
                        $k = "support-{$last->id}-edit";
                        $_SESSION['superform_item_edit'] = $k;
                        // $viewData["support-{$last->id}-edit"] = true;
                        $fragment = "#$k";
                    }
                }





                // si estan enviando el proyecto a revisión
                if (isset($_POST['process_preview']) && isset($_POST['finish'])) {
                    $errors = array();
                    $old_id = $project->id;
                    if ($project->ready($errors)) {

                        if ($_SESSION['project']->id == $old_id) {
                            $_SESSION['project'] = $project;
                        }

                        Library\Message::Info(Text::get('project-review-request_mail-success'));

                        // email a los de goteo
                        if($project->draft)
                            $sent1 = Send::toConsultants('project_preform_to_review_consultant', $project);
                        else
                            $sent1 = Send::toConsultants('project_to_review_consultant', $project);

                        // email al autor
                        $sent2 = Send::toOwner('project_to_review', $project);

                        if ($sent1 && $sent2) {
                            Library\Message::Info(Text::get('project-review-confirm_mail-success'));
                        } else {
                            Library\Message::Error(Text::get('project-review-confirm_mail-fail'));
                        }

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($project->id);
                        $log->populate('El proyecto '.$project->name.' se ha enviado a revision', '/project/'.$project->id, \vsprintf('%s ha inscrito el proyecto %s para <span class="red">revisión</span>, el estado global de la información es del %s', array(
                            Feed::item('user', $project->user->name, $project->user->id),
                            Feed::item('project', $project->name, $project->id),
                            Feed::item('relevant', $project->progress.'%')
                        )));
                        $log->doAdmin('project');
                        unset($log);

                        throw new Redirection("/dashboard?ok");
                    } else {
                        Library\Message::Error(Text::get('project-review-request_mail-fail'));
                        Library\Message::Error(implode('<br />', $errors));
                    }
                }

                // redirect para que no muestre "reenviar los datos" al recargar la página
                // throw new Redirection("/project/edit/{$project->id}/{$step}{$fragment}");
                // Julian 26/09/2014  el redirect provoca que los errores no se muestren corregidos en la lista de abajo



            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST)) {

                // print_r($_POST);
                // print_r($_FILES);
                // throw new Error(Error::INTERNAL, 'EROR: '.print_r($_FILES,1));
                // die;
                // mail de aviso
                $mailHandler = new Library\Mail();
                $mailHandler->to = \GOTEO_FAIL_MAIL;
                $mailHandler->toName = 'Goteo Fail Mail';
                $mailHandler->subject = 'FORM CAPACITY OVERFLOW en '.\SITE_URL;
                $mailHandler->content = 'FORM CAPACITY OVERFLOW en el formulario de proyecto. Llega request method '.$_SERVER['REQUEST_METHOD'].'<hr />';
                $mailHandler->content .= ' y post <pre>' . print_r( $_POST, true) . '</pre><hr />';
                $mailHandler->content .= 'SERVER:<pre>' . print_r( $_SERVER, true) . '</pre>';

                $mailHandler->html = true;
                $mailHandler->template = 11;
                $mailHandler->send($errors);
                unset($mailHandler);


                throw new Error(Error::INTERNAL, 'FORM CAPACITY OVERFLOW');
            }

            // checkear errores
            $project->check($steps);

            // variables para la vista
            $viewData = array(
                'project' => $project,
                'steps' => $steps,
                'step' => $step
            );


            // segun el paso añadimos los datos auxiliares para pintar
            switch ($step) {
                case 'userProfile':
                    $project->user->interests=Model\User\Interest::get($project->user->id);
                    $viewData['user'] = $project->user;
                    $viewData['interests'] = Model\User\Interest::getAll();
                    break;

                case 'userPersonal':
                    $viewData['account'] = Model\Project\Account::get($project->id);
                    break;

                case 'overview':
                    $viewData['categories'] = Model\Project\Category::getAll();
                    $viewData['languages']  = Library\Lang::getall(true); // idiomas activos
                    $viewData['currencies'] = Library\Currency::$currencies; // divisas
                    $viewData['default_currency'] = Library\Currency::DEFAULT_CURRENCY; // divisa por defecto

                    break;

                case 'images':

                    break;

                case 'costs':
                    $viewData['types'] = Model\Project\Cost::types();

                    // convert costs to project currency
                    foreach ($project->costs as &$cost) {
                        // var_dump($cost);
                        $cost->currency = $project->currency;
                        $cost->currency_rate = $project->currency_rate;
                        $cost->currency_html = $currency_data['html'];
                        $cost->amount_original = round($cost->amount * $project->currency_rate);
                        $cost->amount_format = $cost->amount_original.' '.$currency_data['html'];
                        // aquí pueden darse desajustes por redondeo
                    }

                    // para el termómetro horizontal de paso costes
                    $project->mincost = round($project->mincost * $project->currency_rate).' '.$currency_data['html'];
                    $project->maxcost = round($project->maxcost * $project->currency_rate).' '.$currency_data['html'];

                    break;

                case 'rewards':


                    // convert costs to project currency
                    foreach ($project->individual_rewards as &$individual_reward) {
                        // var_dump($cost);
                        $individual_reward->currency = $project->currency;
                        $individual_reward->currency_rate = $project->currency_rate;
                        $individual_reward->currency_html = $currency_data['html'];
                        $individual_reward->amount_original = round($individual_reward->amount * $project->currency_rate);
                        $individual_reward->amount_format = $individual_reward->amount_original.' '.$currency_data['html'];
                        // aquí pueden darse desajustes por redondeo
                    }


                    $viewData['stypes'] = Model\Project\Reward::icons('social');
                    $viewData['itypes'] = Model\Project\Reward::icons('individual');
                    $viewData['licenses'] = Model\Project\Reward::licenses();
                    break;

                case 'supports':
                    $viewData['types'] = Model\Project\Support::types();

                    break;

                case 'preview':
                    $success = array();
                    if (empty($project->errors)) {
                        $success[] = Text::get('guide-project-success-noerrors');
                    }
                    if ($project->finishable) {
                        $success[] = Text::get('guide-project-success-minprogress');
                        $success[] = Text::get('guide-project-success-okfinish');
                    }
                    $viewData['success'] = $success;
                    $viewData['types'] = Model\Project\Cost::types();

                    break;
            }


            // elemento abierto
            if (isset($_SESSION['superform_item_edit'])) {
                $viewData[$_SESSION['superform_item_edit']] = true;
                unset($_SESSION['superform_item_edit']);
            }


            $view = new View (
                'project/edit.html.php',
                $viewData
            );

            return $view;

        }

        public function create () {
            if (! ($user = Session::getUser()) ) {
                $_SESSION['jumpto'] = '/project/create';
                Library\Message::Info(Text::get('user-login-required-to_create'));
                throw new Redirection(SEC_URL.'/user/login');
            }

            if ($_POST['action'] != 'continue' || $_POST['confirm'] != 'true') {
                throw new Redirection("/about/howto");
            }

            $project = new Model\Project(array('owner' => Session::getUserId()));
            if ($project->create(NODE_ID, $errors)) {

                // Evento Feed
                $log = new Feed();
                $log->setTarget($user->id, 'user');
                $log->populate('usuario crea nuevo proyecto', 'admin/projects',
                    \vsprintf('%s ha creado un nuevo proyecto, %s', array(
                        Feed::item('user', $user->name, $user->id),
                        Feed::item('project', $project->name, $project->id))
                    ));
                $log->doAdmin('project');
                unset($log);

                // Si hay que asignarlo a un proyecto
                if (isset($_SESSION['oncreate_applyto'])) {
                    $call = $_SESSION['oncreate_applyto'];

                    $registry = new Model\Call\Project;
                    $registry->id = $project->id;
                    $registry->call = $call;
                    if ($registry->save($errors)) {

                        $callData = Model\Call::getMini($call);
                        // email al autor

                        //  idioma de preferencia del usuario
                        $prefer = Model\User::getPreferences($user->id);
                        $comlang = !empty($prefer->comlang) ? $prefer->comlang : $user->lang;

                        // Obtenemos la plantilla para asunto y contenido
                        $template = Template::get(39, $comlang);

                        // Sustituimos los datos
                        $subject = str_replace('%CALLNAME%', $callData->name, $template->title);

                        // En el contenido:
                        $search  = array('%USERNAME%', '%CALLNAME%', '%CALLERNAME%', '%CALLURL%');
                        $replace = array($user->name, $callData->name, $callData->user->name, SITE_URL.'/call/'.$call);
                        $content = \str_replace($search, $replace, $template->text);


                        $mailHandler = new Library\Mail();

                        $mailHandler->to = $user->email;
                        $mailHandler->toName = $user->name;
                        $mailHandler->subject = $subject;
                        $mailHandler->content = $content;
                        $mailHandler->html = true;
                        $mailHandler->template = $template->id;
                        if ($mailHandler->send($errors)) {
                            Library\Message::Info(Text::get('assign-call-success', $callData->name));
                        } else {
                            Library\Message::Error(Text::get('project-review-confirm_mail-fail'));
                            \mail(\GOTEO_FAIL_MAIL, 'Fallo al enviar mail al crear proyecto asignando a convocatoria', 'Teniamos que enviar email a ' . $user->email . ' con esta instancia <pre>'.print_r($mailHandler, true).'</pre> y ha dado estos errores: <pre>' . print_r($errors, true) . '</pre>');
                        }

                        unset($mailHandler);

                        // Evento feed
                        $log = new Feed();
                        $log->setTarget($call, 'call');
                        $log->populate('nuevo proyecto asignado a convocatoria ' . $call, 'admin/calls/'.$call.'/projects',
                            \vsprintf('Nuevo proyecto %s aplicado a la convocatoria %s', array(
                                Feed::item('project', $project->name, $project->id),
                                Feed::item('call', $call, $call))
                            ));
                        $log->doAdmin('project');
                        unset($log);
                    } else {
                        \mail(\GOTEO_FAIL_MAIL, 'Fallo al asignar a convocatoria al crear proyecto', 'Teniamos que asignar el nuevo proyecto ' . $project->id . ' a la convocatoria ' . $call . ' con esta instancia <pre>'.print_r($register, true).'</pre> y ha dado estos errores: <pre>' . print_r($errors, true) . '</pre>');
                    }
                }

                throw new Redirection("/project/edit/{$project->id}");
            }

            throw new \Goteo\Core\Exception('Error creating project: ' . implode("\n", $errors));
        }

        private function view ($id, $show, $post = null) {
            //activamos la cache para esta llamada
            \Goteo\Core\DB::cache(true);

            $project = Model\Project::get($id, LANG);
            $user = Session::getUser();

            // recompensas
            foreach ($project->individual_rewards as &$reward) {
                $reward->none = false;
                $reward->taken = $reward->getTaken(); // cofinanciadores quehan optado por esta recompensas
                // si controla unidades de esta recompensa, mirar si quedan
                if ($reward->units > 0 && $reward->taken >= $reward->units) {
                    $reward->none = true;
                }
            }

            // retornos adicionales (bonus)
            $project->bonus_rewards = array();
            foreach ($project->social_rewards as $key => &$reward ) {


                if ($reward->bonus) {
                    $project->bonus_rewards[$key] = $reward;
                    unset($project->social_rewards[$key]);
                }
            }

            // mensaje cuando, sin estar en campaña, tiene fecha de publicación
            if ($project->status < 3 && !empty($project->published)) {

                if ($project->published > date('Y-m-d')) {
                    // si la fecha es en el futuro, es que se publicará
                    Library\Message::Info(Text::get('project-willpublish', date('d/m/Y', strtotime($project->published))));
                } else {
                    // si la fecha es en el pasado, es que la campaña ha sido cancelada
                    Library\Message::Info(Text::get('project-unpublished'));
                }

            } elseif ($project->status < 3) {
                // mensaje de no publicado siempre que no esté en campaña
                Library\Message::Info(Text::get('project-not_published'));
            }

            // si lo puede ver
            if (Model\Project::userPublicable($project, Session::getUser())) {

                $project->cat_names = Model\Project\Category::getNames($id);

                if ($show == 'home') {
                    // para el widget embed
                    $project->rewards = array_merge($project->social_rewards, $project->individual_rewards);
                }

                $viewData = array(
                        'project' => $project,
                        'show' => $show
                    );


                // tenemos que tocar esto un poquito para motrar las necesitades no economicas
                if ($show == 'needs-non') {
                    $viewData['show'] = 'needs';
                    $viewData['non-economic'] = true;
                }

                //tenemos que tocar esto un poquito para gestionar los pasos al aportar
                if ($show == 'invest') {

                    // si no está en campaña no pueden estar aqui ni de coña
                    if ($project->status != 3) {
                        Library\Message::Info(Text::get('project-invest-closed'));
                        throw new Redirection('/project/'.$id, Redirection::TEMPORARY);
                    }

                    if ($project->noinvest) {
                        Library\Message::Error(Text::get('investing_closed'));
                        throw new Redirection('/project/'.$id);
                    }

                    $viewData['show'] = 'supporters';

                    // si permite uso de paypal
                    $viewData['allowpp'] = Model\Project\Account::getAllowpp($id);

                    /* pasos de proceso aporte
                     *
                     * 1, 'start': ver y seleccionar recompensa (y cantidad)
                     * 2, 'login': loguear con usuario/contraseña o con email (que crea el usuario automáticamente)
                     * 3, 'confirm': confirmar los datos y saltar a la pasarela de pago
                     * 4, 'ok'/'fail': al volver de la pasarela de pago, la confirmación nos dice si todo bien o algo mal
                     * 5, 'continue': recuperar aporte incompleto (variante de confirm)
                     */

                    // usamos la variable de url $post para movernos entre los pasos
                    $step = (isset($post) && in_array($post, array('start', 'login', 'confirm', 'continue'))) ? $post : 'start';

                    // si llega confirm ya ha terminado el proceso de aporte
                    if (isset($_GET['confirm']) && \in_array($_GET['confirm'], array('ok', 'fail'))) {
                        unset($_SESSION['invest-amount']);
                        // confirmación
                        $step = $_GET['confirm'];
                    } else {
                        // si no, a ver en que paso estamos
                        // guardamos cantidad sin puntos
                        if (isset($_GET['amount']))
                            $_SESSION['invest-amount'] = str_replace(array(',', '.'), '', $_GET['amount']);

                        // si el usuario está validado, recuperamos posible amount y mostramos
                        if ($user instanceof Model\User) {
                            $step = 'confirm';
                        } elseif ($step != 'start' && empty($user)) {
                            // si no está validado solo puede estar en start
                            Library\Message::Info(Text::get('user-login-required-to_invest'));
                            $step = 'start';
                        } elseif ($step == 'start') {
                            // para cuando salte
                            $_SESSION['jumpto'] = SEC_URL.'/project/' .  $id . '/invest/#continue';
                        } else {
                            $step = 'start';
                        }
                    }

                    // recuperación de gotas si está logueado
                    if ($user instanceof Model\User) {
                        $pool = Model\User\Pool::get($user->id);
                        // pasamos la cantidad a la vista
                        $viewData['pool'] = (int) $pool->amount;
                    }

                    /*
                    elseif (isset($_SESSION['pre-invest'])) {
                        // aporte incompleto, puede ser que aun no esté logueado
                        if (empty($user)) {
                            $step = 'login';
                        } else {
                            $step = 'confirm';
                        }
                    }
                     *
                     */

                    /*
                        Library\Message::Info(Text::get('user-login-required-to_invest'));
                        throw new Redirection("/user/login");
                     */

                    $viewData['step'] = $step;
                }

                // -- Mensaje azul molesto para usuarios no registrados
                if ($show == 'messages') {
                    $project->messages = Model\Message::getAll($project->id);

                    if (empty($user)) {
                        Library\Message::Info(Text::html('user-login-required'));
                    }
                }

                // posts
                if ($show == 'updates') {
                    // sus entradas de novedades
                    $blog = Model\Blog::get($project->id);
                    // si está en modo preview, ponemos  todas las entradas, incluso las no publicadas
                    if (isset($_GET['preview']) && $_GET['preview'] == $user->id) {
                        $blog->posts = Model\Blog\Post::getAll($blog->id, null, false);
                    }

                    $viewData['blog'] = $blog;

                    $viewData['post'] = $post;
                    $viewData['owner'] = $project->owner;

                    if (empty($user)) {
                        Library\Message::Info(Text::html('user-login-required'));
                    }
                }

                if ($show == 'messages' && $project->status < 3) {
                    Library\Message::Info(Text::get('project-messages-closed'));
                }

                return View::get('project/view.html.php', $viewData);

            } else {
                // no lo puede ver
                throw new Redirection("/");
            }
        }

        //-----------------------------------------------
        // Métodos privados para el tratamiento de datos
        // del save y remove de las tablas relacionadas se enmcarga el model/project
        // primero añadir y luego quitar para que no se pisen los indices
        // En vez del hidden step, va a comprobar que esté definido en el post el primer campo del proceso
        //-----------------------------------------------
        /*
         * Paso 1 - PERFIL
         */
        private function process_userProfile(&$project, &$errors) {
            if (!isset($_POST['process_userProfile'])) {
                return false;
            }

            $user = Model\User::get($project->owner);

            // tratar la imagen y ponerla en la propiedad avatar
            // __FILES__

            $fields = array(
                'user_name'=>'name',
                'user_location'=>'location',
                'user_avatar'=>'avatar',
                'user_about'=>'about',
//                'user_keywords'=>'keywords',
//                'user_contribution'=>'contribution',
                'user_facebook'=>'facebook',
                'user_google'=>'google',
                'user_twitter'=>'twitter',
                'user_identica'=>'identica',
                'user_linkedin'=>'linkedin'
            );

            foreach ($fields as $fieldPost=>$fieldTable) {
                if (isset($_POST[$fieldPost])) {
                    $user->$fieldTable = $_POST[$fieldPost];
                }
            }

            // Avatar
            if (isset($_FILES['avatar_upload']) && $_FILES['avatar_upload']['error'] != UPLOAD_ERR_NO_FILE) {
                $user->avatar = $_FILES['avatar_upload'];
            }

            // tratar si quitan la imagen
            if (!empty($_POST['avatar-' . $user->avatar->hash .  '-remove'])) {
                $user->avatar->remove($errors);
                $user->avatar = null;
            }

            $user->interests = $_POST['user_interests'];

            //tratar webs existentes
            foreach ($user->webs as $i => &$web) {
                // luego aplicar los cambios

                if (isset($_POST['web-'. $web->id . '-url'])) {
                    $web->url = $_POST['web-'. $web->id . '-url'];
                }

                //quitar las que quiten
                if (!empty($_POST['web-' . $web->id .  '-remove'])) {
                    unset($user->webs[$i]);
                }

            }

            //tratar nueva web
            if (!empty($_POST['web-add'])) {
                $user->webs[] = new Model\User\Web(array(
                    'url'   => 'http://'
                ));
            }

            /// este es el único save que se lanza desde un metodo process_
            $user->save($project->errors['userProfile']);

            // si hay errores en la imagen hay que mostrarlos
            if (!empty($project->errors['userProfile']['image'])) {
                $project->errors['userProfile']['avatar'] = $project->errors['userProfile']['image'];
            }


            // actualizar perfil propio solo si es el impulsor
            if (Session::getUserId() == $project->owner) {
                Model\User::flush();
            }
            $project->user = $user;
            return true;
        }

        /*
         * Paso 2 - DATOS PERSONALES
         */
        private function process_userPersonal(&$project, &$errors) {
            if (!isset($_POST['process_userPersonal'])) {
                return false;
            }

            // campos que guarda este paso
            $fields = array(
                'contract_name',
                'contract_nif',
                'contract_email',
                'phone',
//                'contract_entity',
                'contract_birthdate',
//                'entity_office',
                'entity_name',
//                'entity_cif',
                'address',
                'zipcode',
                'location',
                'country',
//                'secondary_address',
//                'post_address',
//                'post_zipcode',
//                'post_location',
//                'post_country'
            );

            $personalData = array();

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $project->$field = $_POST[$field];
                    $personalData[$field] = $_POST[$field];
                }
            }

            if (!$_POST['secondary_address']) {
                $project->post_address = null;
                $project->post_zipcode = null;
                $project->post_location = null;
                $project->post_country = null;
            }

            // actualizamos estos datos en los personales del usuario
            if (!empty ($personalData)) {
                Model\User::setPersonal($project->owner, $personalData, true);
            }

            // cuentas bancarias
            $ppacc = (!empty($_POST['paypal'])) ? $_POST['paypal'] : '';
            $bankacc = (!empty($_POST['bank'])) ? $_POST['bank'] : '';

            // primero checkeamos si la cuenta Paypal es tipo email
            if (!isset($project->called) && !empty($ppacc) && !Check::mail($ppacc)) {
                $project->errors['userPersonal']['paypal'] = Text::get('validate-project-paypal_account');
            } else {
                $project->okeys['userPersonal']['paypal'] = true;
            }

            $accounts = Model\Project\Account::get($project->id);
            $accounts->paypal = $ppacc;
            $accounts->bank = $bankacc;
            $accounts->save($project->errors['userPersonal']);


            return true;
        }

        /*
         * Paso 3 - DESCRIPCIÓN
         */

        private function process_overview(&$project, &$errors) {
            if (!isset($_POST['process_overview'])) {
                return false;
            }

            // preveer cambio de divisa
            if ( $_POST['currency'] != $project->currency || $_POST['currency'] != $_SESSION['currency'] ) {
                $_SESSION['currency'] = Library\Currency::set($_POST['currency']); // divisa en la que ve la web

                // si el que edita es el impulsor, cambia su preferencia
                if (Session::getUserId() == $project->owner) {
                    Model\User::setPreferences($project->owner, array('currency'=>$_SESSION['currency']));
                }
            }

            // campos que guarda este paso
            // image, media y category  van aparte
            $fields = array(
                'name',
                'subtitle',
                'lang',
                'currency',
                'description',
                'motivation',
                'video',
                'video_usubs',
                'about',
                'goal',
                'related',
                'spread',
                'reward',
                'keywords',
                'media',
                'media_usubs',
                'project_location'
            );

            foreach ($fields as $field) {
                $project->$field = $_POST[$field];
            }

            // Media
            if (!empty($project->media)) {
                $project->media = new Model\Project\Media($project->media);
            }
            // Video de motivación
            if (!empty($project->video)) {
                $project->video = new Model\Project\Media($project->video);
            }

            //categorias
            // añadir las que vienen y no tiene
            $tiene = $project->categories;
            if (isset($_POST['categories'])) {
                $viene = $_POST['categories'];
                $quita = array_diff($tiene, $viene);
            } else {
                $quita = $tiene;
            }
            $guarda = array_diff($viene, $tiene);
            foreach ($guarda as $key=>$cat) {
                $category = new Model\Project\Category(array('id'=>$cat,'project'=>$project->id));
                $project->categories[] = $category;
            }

            // quitar las que tiene y no vienen
            foreach ($quita as $key=>$cat) {
                unset($project->categories[$key]);
            }

            $quedan = $project->categories; // truki para xdebug


            return true;
        }

        /*
         * Paso 3b - IMÁGENES
         */

        private function process_images(&$project, &$errors) {
            if (!isset($_POST['process_images'])) {
                return false;
            }


            // tratar la imagen que suben
            if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] != UPLOAD_ERR_NO_FILE) {
                $project->image = $_FILES['image_upload'];
            }

            // tratar las imagenes que quitan
            foreach ($project->images as $key=>$image) {
                if (!empty($_POST["gallery-".$image->hash."-remove"])) {
                    $image->remove($errors, 'project');
                    // recalculamos las galerias e imagen

                    // getGalleries en Project\Image  procesa todas las secciones
                    $galleries = Model\Project\Image::getGalleries($project->id);
                    Model\Project\Image::setImage($project->id, $galleries['']);

                    unset($project->images[$key]);
                }
            }

            return true;
        }

        /*
         * Paso 4 - COSTES
         */
        private function process_costs(&$project, &$errors) {
            if (!isset($_POST['process_costs'])) {
                return false;
            }

            /* aligerando
            if (isset($_POST['resource'])) {
                $project->resource = $_POST['resource'];
            }
            */

            //tratar costes existentes
            foreach ($project->costs as $key => $cost) {

                if (!empty($_POST["cost-{$cost->id}-remove"])) {
                    unset($project->costs[$key]);
                    continue;
                }

                if (isset($_POST['cost-' . $cost->id . '-cost'])) {

                    $cost->cost = $_POST['cost-' . $cost->id . '-cost'];
                    $cost->description = $_POST['cost-' . $cost->id .'-description'];

                    $new_amount = $_POST['cost-' . $cost->id . '-amount'];
                    // ajuste divisa proyecto
                    if ($project->currency != Library\Currency::DEFAULT_CURRENCY) {
                        // convertir solo al modificar
                        if ($new_amount != $cost->amount_original) {
                            $new_amount = $new_amount / $project->currency_rate;
                        }
                    }

                    $cost->amount = $new_amount;
                    $cost->type = $_POST['cost-' . $cost->id . '-type'];
                    $cost->required = $_POST['cost-' . $cost->id . '-required'];
                    $cost->from = $_POST['cost-' . $cost->id . '-from'];
                    $cost->until = $_POST['cost-' . $cost->id . '-until'];
                }

            }

            //añadir nuevo coste
            if (!empty($_POST['cost-add'])) {

                $project->costs[] = new Model\Project\Cost(array(
                    'project' => $project->id,
                    'cost'  => 'Nueva tarea',
                    'type'  => 'task',
                    'required' => 1
                /*,
                    'from' => date('Y-m-d'),
                    'until' => date('Y-m-d')
                */

                ));

            }

            // ronda unica
            $project->one_round = !empty($_POST['one_round']) ? 1 : 0;

            // solicita ayuda en costes
            $project->help_cost = !empty($_POST['help_cost']) ? 1 : 0;

            return true;
        }

        /*
         * Paso 5 - RETORNO
         */
        private function process_rewards(&$project, &$errors) {
            if (!isset($_POST['process_rewards'])) {
                return false;
            }

            $types = Model\Project\Reward::icons('');

            //tratar retornos sociales
            foreach ($project->social_rewards as $k => $reward) {

                if (!empty($_POST["social_reward-{$reward->id}-remove"])) {
                    unset($project->social_rewards[$k]);
                    continue;
                }

                if (isset($_POST['social_reward-' . $reward->id . '-reward'])) {
                    $reward->reward = $_POST['social_reward-' . $reward->id . '-reward'];
                    $reward->description = $_POST['social_reward-' . $reward->id . '-description'];
                    $reward->icon = $_POST['social_reward-' . $reward->id . '-icon'];
                    if ($reward->icon == 'other') {
                        $reward->other = $_POST['social_reward-' . $reward->id . '-other'];
                    }
                    $reward->license = $_POST['social_reward-' . $reward->id . '-' . $reward->icon . '-license'];
                    $reward->icon_name = $types[$reward->icon]->name;
                }

            }

            // retornos individuales
            foreach ($project->individual_rewards as $k => $reward) {

                if (!empty($_POST["individual_reward-{$reward->id}-remove"])) {
                    unset($project->individual_rewards[$k]);
                    continue;
                }

                if (isset($_POST['individual_reward-' . $reward->id .'-reward'])) {
                    $reward->reward = $_POST['individual_reward-' . $reward->id .'-reward'];
                    $reward->description = $_POST['individual_reward-' . $reward->id . '-description'];
                    $reward->icon = $_POST['individual_reward-' . $reward->id . '-icon'];
                    if ($reward->icon == 'other') {
                        $reward->other = $_POST['individual_reward-' . $reward->id . '-other'];
                    }

                    $new_amount = $_POST['individual_reward-' . $reward->id . '-amount'];
                    // ajuste divisa proyecto
                    if ($project->currency != Library\Currency::DEFAULT_CURRENCY) {
                        // convertir solo al modificar
                        if ($new_amount != $reward->amount_original) {
                            $new_amount = $new_amount / $project->currency_rate;
                        }
                    }
                    $reward->amount = $new_amount;

                    $reward->units = $_POST['individual_reward-' . $reward->id . '-units'];
                    $reward->icon_name = $types[$reward->icon]->name;
                }

            }

            // tratar nuevos retornos
            if (!empty($_POST['social_reward-add'])) {
                $project->social_rewards[] = new Model\Project\Reward(array(
                    'type'      => 'social',
                    'project'   => $project->id,
                    'reward'    => 'Nuevo retorno colectivo',
                    'icon'      => '',
                    'license'   => ''

                ));
            }

            if (!empty($_POST['individual_reward-add'])) {
                $project->individual_rewards[] = new Model\Project\Reward(array(
                    'type'      => 'individual',
                    'project'   => $project->id,
                    'reward'    => 'Nueva recompensa individual',
                    'icon'      => '',
                    'amount'    => '',
                    'units'     => ''
                ));
            }

             // solicita ayuda con licencias
            $project->help_license = !empty($_POST['help_license']) ? 1 : 0;

            return true;

        }

        /*
         * Paso 6 - COLABORACIONES
         */
         private function process_supports(&$project, &$errors) {
            if (!isset($_POST['process_supports'])) {
                return false;
            }

            // tratar colaboraciones existentes
            foreach ($project->supports as $key => $support) {

                // quitar las colaboraciones marcadas para quitar
                if (!empty($_POST["support-{$support->id}-remove"])) {
                    unset($project->supports[$key]);
                    continue;
                }

                if (isset($_POST['support-' . $support->id . '-support'])) {
                    $support->support = $_POST['support-' . $support->id . '-support'];
                    $support->description = $_POST['support-' . $support->id . '-description'];
                    $support->type = $_POST['support-' . $support->id . '-type'];

                    if (!empty($support->thread)) {
                        // actualizar ese mensaje
                        $msg = Model\Message::get($support->thread);
                        $msg->date = date('Y-m-d');
                        $msg->message = "{$support->support}: {$support->description}";
                        $msg->blocked = true;
                        $msg->save($errors);
                    } else {
                        // grabar nuevo mensaje
                        $msg = new Model\Message(array(
                            'user'    => $project->owner,
                            'project' => $project->id,
                            'date'    => date('Y-m-d'),
                            'message' => "{$support->support}: {$support->description}",
                            'blocked' => true
                            ));
                        if ($msg->save($errors)) {
                            // asignado a la colaboracion como thread inicial
                            $support->thread = $msg->id;
                        }
                    }

                }

            }

            // añadir nueva colaboracion
            if (!empty($_POST['support-add'])) {
                $project->supports[] = new Model\Project\Support(array(
                    'project'       => $project->id,
                    'support'       => 'Nueva colaboración',
                    'type'          => 'task',
                    'description'   => ''
                ));
            }

            return true;
        }

        /*
         * Paso 7 - PREVIEW
         * No hay nada que tratar porque aq este paso no se le envia nada por post
         */
        private function process_preview(&$project) {
            if (!isset($_POST['process_preview'])) {
                return false;
            }

            if (!empty($_POST['comment'])) {
                $project->comment = $_POST['comment'];
            }

            return true;
        }
        //-------------------------------------------------------------
        // Hasta aquí los métodos privados para el tratamiento de datos
        //-------------------------------------------------------------
   }

}
