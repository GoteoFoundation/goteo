<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Application\Session,
        Goteo\Model,
        Goteo\Library\Message,
        Goteo\Library\Feed,
        Goteo\Library\Page,
        Goteo\Library\Text;

    class Dashboard extends \Goteo\Core\Controller {

        public function index() {
            throw new Redirection('/dashboard/activity');
        }

        /*
         * Sección, Mi actividad
         * Opciones:
         *      'summary' portada y proyectos del usuario y a los que ha aportado,
         *      'donor' gestión del certificado de donativo
         *      'comunity'//'wall' relacion con la comunidad
         */
        public function activity($option = 'summary', $action = 'view') {

            $user = $_SESSION['user'];

            $viewData = array(
                                'menu' => self::menu(),
                                'section' => __FUNCTION__,
                                'option' => $option,
                                'action' => $action
                            );

            // portada
            if ($option == 'summary') {
                $page = Page::get('dashboard');
                $viewData['message'] = \str_replace('%USER_NAME%', $_SESSION['user']->name, $page->content);
                $viewData['lists']   = Dashboard\Activity::projList($user);
                $viewData['status']  = Model\Project::status();
            }

            // gestión de certificado
            if ($option == 'donor')  {
                $viewData['donation'] = Dashboard\Activity::donor($user, $action);
            }


            // si es un salto a otro panel
            if (in_array($option, array('admin', 'review', 'translate'))) {

                // si tiene algún rol de admin
                if ( $option == 'admin' &&  ( isset($_SESSION['user']->roles['admin']) || isset($_SESSION['user']->roles['superadmin']) ) )
                    throw new Redirection('/'.$option, Redirection::TEMPORARY);
                else
                    throw new Redirection('/dashboard', Redirection::TEMPORARY);

                // si tiene rol de revisor
                if ( $option == 'review' && isset($_SESSION['user']->roles['checker']) )
                    throw new Redirection('/'.$option, Redirection::TEMPORARY);
                else
                    throw new Redirection('/dashboard', Redirection::TEMPORARY);

                // si tiene rol de traductor
                if ( $option == 'translate' &&  isset($_SESSION['user']->roles['admin']) || isset($_SESSION['user']->roles['superadmin']) || isset($_SESSION['user']->roles['translator']) )
                    throw new Redirection('/'.$option, Redirection::TEMPORARY);
                else
                    throw new Redirection('/dashboard', Redirection::TEMPORARY);

            }

            return new View('dashboard/index.html.php', $viewData);
        }

        /*
         * Seccion, Mi perfil
         * Opciones:
         *      'public' perfil público (paso 1),
         *      'personal' datos personales (paso 2),
         *      'access' configuracion (cambio de email y contraseña)
         *
         */

        public function profile($option = 'profile', $action = 'edit') {
            // tratamos el post segun la opcion y la acion
            $user = Session::getUser();

            // salto al perfil público
            if ($option == 'public') throw new Redirection('/user/profile/' . $user->id);

            // vip/recomendador tiene una imagen adicional
            $vip = ($option == 'profile' && isset($user->roles['vip'])) ? Model\User\Vip::get($user->id) : null;

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $log_action = null;
                $errors = array();

                switch ($option) {
                    // perfil publico
                    case 'profile':
                        Dashboard\Profile::process_profile($user, $vip, $errors, $log_action);
                        break;

                    // datos personales
                    case 'personal':
                        Dashboard\Profile::process_personal($user->id, $errors, $log_action);
                        break;

                    // geolocalización (tiene un subcontrolador específico)
                    case 'location':
                        $errors = Dashboard\Location::process();
                        break;

                    //cambio de email y contraseña
                    case 'access':
                        Dashboard\Profile::process_access($user, $errors, $log_action);
                        break;

                    // preferencias de notificación
                    case 'preferences':
                        Dashboard\Profile::process_preferences($user->id, $errors, $log_action);
                        break;
                }

                if (!empty($log_action)) {
                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($user->id, 'user');
                    $log->populate('usuario ' . $log_action . ' (dashboard)', '/admin/users', \vsprintf('%s ha %s desde su dashboard', array(
                                Feed::item('user', $user->name, $user->id),
                                Feed::item('relevant', $log_action)
                            )));
                    $log->doAdmin('user');
                    unset($log);
                }
            }

            $viewData = array(
                'menu' => self::menu(),
                'section' => __FUNCTION__,
                'option' => $option,
                'action' => $action,
                'errors' => $errors,
                'user' => $user
            );

            switch ($option) {
                case 'profile':
                    $viewData['interests'] = Model\User\Interest::getAll();

                    if ($_POST) {
                        foreach ($_POST as $k => $v) {
                            if (!empty($v) && preg_match('/web-(\d+)-edit/', $k, $r)) {
                                $viewData[$k] = true;
                                break;
                            }
                        }
                    }

                    if (!empty($_POST['web-add'])) {
                        $last = end($user->webs);
                        if ($last !== false) {
                            $viewData["web-{$last->id}-edit"] = true;
                        }
                    }

                    if (isset($user->roles['vip'])) {
                        $viewData['vip'] = Model\User\Vip::get($user->id);
                    }

                    break;
                case 'personal':
                    $viewData['personal'] = Model\User::getPersonal($user->id);
                    break;
                case 'location':
                    // si geolocalizado
                    $viewData['geolocation'] = $user->geoloc;
                    if($viewData['geolocation'] && $user->unlocable) $viewData['geolocation'] = false;

                    break;
                case 'access':
                    // si es recover, en contraseña actual tendran que poner el username
                    if ($action == 'recover') {
                        $viewData['message'] = Text::get('dashboard-password-recover-advice');
                    }
                    break;
                case 'preferences':
                    $viewData['preferences'] = Model\User::getPreferences($user->id);

                    //Si no hay un idioma preferido para notificaciones
                    if(!$viewData['preferences']->comlang)
                        $viewData['preferences']->comlang=LANG;

                    //Si no hay una moneda preferida usamos la de sesión
                    if(!$viewData['preferences']->currency)
                        $viewData['preferences']->currency=$_SESSION['currency'];

                    break;
            }

            return new View('dashboard/index.html.php', $viewData);
        }

        /*
         * Seccion, Mis proyectos
         * Opciones:
         *      'actualizaciones' blog del proyecto
         *      'editar colaboraciones' para modificar los mensajes de colaboraciones (no puede editar el proyecto y ya estan publicados)
         *      'widgets' ofrece el código para poner su proyecto en otras páginas (vertical y horizontal)
         *      'licencia' el acuerdo entre goteo y el usuario, licencia cc-by-nc-nd, enlace al pdf
         *      'gestionar retornos' resumen recompensas/cofinanciadores/conseguido  y lista de cofinanciadores y recompensas esperadas
         *      'participantes' para comunicarse con los participantes en mensajes
         *      'pagina publica' enlace a la página pública del proyecto
         *
         *      NEW: 'analytics' grafico de evolución de recaudación del proyecto
         *
         */
        public function projects($option = 'summary', $action = 'list', $id = null) {

            $user = $_SESSION['user'];

            $errors = array();

            // verificación de proyectos y proyecto de trabajo
            list($project, $projects) = Dashboard\Projects::verifyProject($user, $action, $option);

            // verificación de registros según $option
            switch ($option) {
                case 'updates':
                    // teniendo proyecto de trabajo, comprobar si el proyecto esta en estado de tener blog
                    $blog = Dashboard\Projects::verifyBlog($project);

                    // SubControlador para add, edit, delete y list
                    // devuelve $post en las acciones add y edit y $posts en delete y list
                    // maneja por referencia $action, $posts y $errors
                    list($post, $posts) = Dashboard\Projects::prepare_updates($action, $id, $blog->id);

                    break;

                case 'rewards':
                    // sacaexcel de cofinanciadores
                    if ($action == 'table') {
                        $response = new \Goteo\Controller\Sacaexcel;
                        return $response->index('investors', $project->id);
                    }

                    break;

                case 'commons':
                    if ($project->status != 4 && empty($project->social_rewards)) {
                        Message::Error('Este proyecto no tiene retornos colectivos');
                        throw new Redirection('/dashboard/projects/');
                    }

                    break;

                case 'supports':
                    break;
            }

            // procesamiento de formularios
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                switch ($option) {
                    // gestionar retornos (o mensaje a los mensajeros)
                    case 'messengers':
                    case 'rewards':
                        // sacamos del post el filtro y el orden
                        if ($action == 'filter') {
                            $_SESSION['dashboard-rewards-filter'] = (isset($_POST['filter'])) ? $_POST['filter'] : $_SESSION['dashboard-rewards-filter'];
                            $_SESSION['dashboard-rewards-order']  = (isset($_POST['order']))  ?  $_POST['order'] : $_SESSION['dashboard-rewards-order'];
                        }

                        //procesamos el envio de mails
                        if ($action == 'message') {
                            Dashboard\Projects::process_mailing($option, $project);
                        }
                        break;

                    // contrato, pahora mismo nada
                    case 'contract':
                        // Dashboard\Projects::process_contract($project, $errors);
                        break;

                    // colaboraciones
                    case 'supports':
                        if ($action == 'save') $project = Dashboard\Projects::process_supports($project, $errors);
                        break;

                    case 'updates':
                        // verificación: si no llega blog correcto no lo procesamos
                        if (empty($_POST['blog']) || $_POST['blog'] != $blog->id) throw new Redirection('/dashboard/projects/summary');

                        list($action, $id) = Dashboard\Projects::process_updates($action, $project, $errors);

                        // redirección si han pulsado guardar
                        if ($action == 'edit') {
                            throw new Redirection('/dashboard/projects/'.$option.'/'.$action.'/'.$id);
                        } else {
                            throw new Redirection('/dashboard/projects/'.$option.'/'.$action);
                        }

                        break;

                    // gestión retornos
                    case 'commons':
                        // se maneja más abajo según action (add/edit)
                        break;

                }
            }

            // Preparación de la vista

            // view data basico para esta seccion
            $viewData = array(
                'menu' => self::menu(),
                'section' => __FUNCTION__,
                'option' => $option,
                'action' => $action,
                'projects' => $projects,
                'errors' => $errors
            );


            switch ($option) {
                case 'summary':
                    // los datos json de invests y visitors_data
                    $viewData['data'] = Dashboard\Projects::graph($project->id);
                    break;

                // gestionar recompensas
                case 'rewards':
                    // recompensas ofrecidas
                    $viewData['rewards'] = Model\Project\Reward::getAll($project->id, 'individual');
                    // aportes para este proyecto
                    $viewData['invests'] = Model\Invest::getAll($project->id);
                    // ver por (esto son orden y filtros)
                    $viewData['filter'] = $_SESSION['dashboard-rewards-filter'];
                    $viewData['order'] = $_SESSION['dashboard-rewards-order'];
                    break;

                // gestionar retornos
                case 'commons':

                    $icons = Model\Icon::getAll('social');
                    foreach ($icons as $key => $icon) {
                        $icons[$key] = $icon->name;
                    }
                    $licenses = Model\License::getList();
                    $viewData['icons'] = $icons;
                    $viewData['licenses'] = $licenses;
                    // ruta
                    $viewData['path'] = '/dashboard/projects/commons';

                    switch ($action) {
                        // acciones sobre retorno
                        case 'add':
                        case 'edit':
                            // editar un retorno colectivo
                            if (empty($_GET['reward_id'])) {
                                $reward = new Model\Project\Reward;
                                $reward->id = '';
                                $reward->project = $id;
                                $reward->bonus = 1;
                            } else {
                                $reward = Model\Project\Reward::get($_GET['reward_id']);

                                // en dashboard, impulsor solo puede editar retornos bonus y solo cuando proyecto estado 4
                                if ( $project->status != 4 && !$reward->bonus ) {
                                    throw new Redirection('/dashboard/projects/commons');
                                }
                            }

                            $stypes = Model\Project\Reward::icons('social');

                            // si llega post -> procesamos el formulario
                            if (isset($_POST['social_reward-' . $reward->id . '-reward'])) {
                                $errors = array();

                                $reward->reward = $_POST['social_reward-' . $reward->id . '-reward'];
                                $reward->description = $_POST['social_reward-' . $reward->id . '-description'];
                                $reward->icon = $_POST['social_reward-' . $reward->id . '-icon'];
                                if ($reward->icon == 'other') {
                                    $reward->other = $_POST['social_reward-' . $reward->id . '-other'];
                                }
                                $reward->license = $_POST['social_reward-' . $reward->id . '-' . $reward->icon . '-license'];
                                $reward->icon_name = $icons[$reward->icon];

                                if ($reward->save($errors)) {
                                    throw new Redirection('/dashboard/projects/commons');
                                } else {
                                    Message::Error(implode('<br />', $errors));
                                }
                            }

                            $viewData['reward'] = $reward;
                            $viewData['stypes'] = $stypes;

                            break;

                        case 'delete':
                            // eliminar retorno
                            if (isset($_GET['reward_id'])) {
                                $errors = array();
                                $reward = Model\Project\Reward::get($_GET['reward_id']);

                                // en dashboard, impulsor solo puede eliminar retornos bonus y solo cuando proyecto estado 4
                                if ( $project->status != 4 || !$reward->bonus ) {
                                    throw new Redirection('/dashboard/projects/commons');
                                }

                                if (!$reward->remove($errors)) {
                                    Message::Error(implode('<br />', $errors));
                                }
                            }
                            throw new Redirection('/dashboard/projects/commons');
                            break;
                    }

                    break;

                // listar mensajeadores
                case 'messengers':
                    $viewData['messengers'] = Model\Message::getMessengers($project->id);
                    break;

                // editar colaboraciones
                case 'supports':
                    $viewData['types'] = Model\Project\Support::types();

                    // para mantener registros desplegados
                    if ($_POST) {
                        foreach ($_POST as $k => $v) {
                            if (!empty($v) && preg_match('/support-(\d+)-edit/', $k, $r)) {
                                $viewData[$k] = true;
                                break;
                            }
                        }
                    }

                    if (!empty($_POST['support-add'])) {
                        $last = end($project->supports);
                        if ($last !== false) {
                            $viewData['support-' . $last->id . '-edit'] = true;
                        }
                    }

                  //  $project->supports = Model\Project\Support::getAll($project->id);
                    break;

                // publicar actualizaciones
                case 'updates':
                    $viewData['blog'] = $blog;
                    $viewData['posts'] = $posts;
                    $viewData['post'] = $post;
                    $viewData['ckeditor'] = true;
                    break;

                // datos de contrato y mensaje segun estado del proceso
                case 'contract':
                    if ($project->status < 3) {
                        $contract = null;
                        $show = 'off';
                    } else {

                        // veamos si quiere editarlo
                        if ($action == 'edit')
                            throw new Redirection('/contract/edit/'.$project->id);

                        // o si quiere ver el borrador/pdf
                        if ($action == 'view')
                            throw new Redirection('/contract/'.$project->id);


                        // a ver si tiene registro
                        $contract = Model\Contract::get($project->id);

                        if (!$contract) {
                            $show = 'campaign'; // el proyecto sigue en campaña, aun no se puede gestionar el contrato
                        } elseif ($contract->status->closed) {
                            $show = 'fulfilled'; // se ha finiquitado el contrato
                        } elseif ($contract->status->payed) {
                            $show = 'payed'; // ya se ha realizado el pago
                        } elseif ($contract->status->recieved) {
                            $show = 'recieved'; // goteo ha recibido el contrato firmado
                        } elseif ($contract->status->ready) {
                            $show = 'ready'; // el pdf está listo para descargarse
                        } elseif ($contract->status->admin) {
                            $show = 'review'; // los datos están siendo revisados por el admín
                        } elseif ($contract->status->owner) {
                            $show = 'closed'; // el formuladio está cerrado, el contrato está en cola para ser revisado
                        } else {
                            $show = 'edit'; // hay registro y se puede editar
                        }
                    }

                    $viewData['contract'] = $contract;
                    $viewData['content'] = Dashboard\Projects::prepare_content($show);
                    $viewData['footer'] = Dashboard\Projects::prepare_content('siempre');

                    break;
            }

            $viewData['project'] = $project;

            return new View('dashboard/index.html.php', $viewData);
        }
        // Fin de la sección Mis proyectos



        /*
         * Seccion, Mis traducciones
         * Opciones:
         *      'profile'  <-- ojo, con esto se traduce la informacion del usuario
         *      'overview'
         *      'costs'
         *      'rewards'
         *      'supports'
         *      'updates'
         *
         */

        public function translates($option = 'overview', $action = 'list', $id = null) {

            $user = $_SESSION['user'];

            $errors = array();

            $langs = \Goteo\Library\Lang::getAll();

            if ($action == 'lang' && !empty($_POST['lang'])) {
                $_SESSION['translate_lang'] = $_POST['lang'];
            } elseif (empty($_SESSION['translate_lang'])) {
                $_SESSION['translate_lang'] = 'en';
            }

            $projects = Model\User\Translate::getMyProjects($user->id);
            $calls = Model\User\Translate::getMyCalls($user->id);
            $nodes = Model\User\Translate::getMyNodes($user->id);


            // al seleccionar controlamos: translate_type y translateproject/translate_call
            if ($action == 'select' && !empty($_POST['type'])) {
                unset($_SESSION['translate_project']); // quitamos el proyecto de traducción
                unset($_SESSION['translate_call']); // quitamos la convocatoria de traducción
                unset($_SESSION['translate_node']); // quitamos el nodo de traducción

                $type = $_POST['type'];
                if (!empty($_POST[$type])) {
                    $_SESSION['translate_type'] = $type;
                    $_SESSION['translate_' . $type] = $_POST[$type];
                } else {
                    $_SESSION['translate_type'] = 'profile';
                }
            }

            // view data basico para esta seccion
            $viewData = array(
                'menu' => self::menu(),
                'section' => __FUNCTION__,
                'option' => $option,
                'action' => $action,
                'langs' => $langs,
                'projects' => $projects,
                'calls' => $calls,
                'nodes' => $nodes,
                'errors' => $errors,
                'success' => $success
            );

            // aqui, segun lo que este traduciendo, necesito tener un proyecto de trabajo, una convocatoria o mi perfil personal
            switch ($_SESSION['translate_type']) {
                case 'project':
                    try {
                        // si lo tenemos en sesion una id de proyecto
                        if ( isset($_SESSION['translate_project']) ) {

                            $proj_id = (is_object($_SESSION['translate_project'])) ? $_SESSION['translate_project']->id : $_SESSION['translate_project'];

                            // instancia original
                            $project_original = Model\Project::get($proj_id, null); // $lang = null para sacar idioma original

                            // instancia traducción
                            $project = Model\Project::get($proj_id, $_SESSION['translate_lang']);

                        } else {

                            // no tenemos proyecto
                            $project_original = null;
                            $project = null;
                        }

                    } catch (\Goteo\Core\Error $e) {
                        Message::Error('Ha fallado al cargar los datos del proyecto');
                        $_SESSION['translate_type'] = 'profile';
                        throw new Redirection('/dashboard/translates');
                    }

                    if (!$project instanceof Model\Project || !$project_original instanceof Model\Project) {
                        Message::Error('Ha fallado al cargar los datos del proyecto');
                        $_SESSION['translate_type'] = 'profile';
                        throw new Redirection('/dashboard/translates');
                    }

                    $_SESSION['translate_project'] = $project;
                    $project_original->lang_name = $langs[$project_original->lang]->name;

                    // quitamos el idioma original del desplegable de idiomas
                    unset($viewData['langs'][$project_original->lang]);

//// Control de traduccion de proyecto
                    if ($option == 'updates') {
                        // sus novedades
                        $blog = Model\Blog::get($project->id);
                        if ($action != 'edit') {
                            $action = 'list';
                        }
                    }

                    // tratar lo que llega por post para guardar los datos
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        switch ($option) {
                            case 'profile':
                                if ($action == 'save') {
                                    $user = Model\User::get($_POST['id'], $_SESSION['translate_lang']);
                                    $user->about_lang = $_POST['about'];
                                    $user->keywords_lang = $_POST['keywords'];
                                    $user->contribution_lang = $_POST['contribution'];
                                    $user->lang = $_SESSION['translate_lang'];
                                    $user->saveLang($errors);
                                }
                                break;

                            case 'overview':
                                if ($action == 'save') {
                                    $project->description_lang = $_POST['description'];
                                    $project->motivation_lang = $_POST['motivation'];
                                    $project->video_lang = $_POST['video'];
                                    $project->about_lang = $_POST['about'];
                                    $project->goal_lang = $_POST['goal'];
                                    $project->related_lang = $_POST['related'];
                                    $project->reward_lang = $_POST['reward'];
                                    $project->keywords_lang = $_POST['keywords'];
                                    $project->media_lang = $_POST['media'];
                                    $project->subtitle_lang = $_POST['subtitle'];
                                    $project->lang_lang = $_SESSION['translate_lang'];
                                    $project->saveLang($errors);
                                }
                                break;

                            case 'costs':
                                if ($action == 'save') {

                                    foreach ($project->costs as $key => $cost) {
                                        if (isset($_POST['cost-' . $cost->id . '-cost'])) {
                                            $cost->cost_lang = $_POST['cost-' . $cost->id . '-cost'];
                                            $cost->description_lang = $_POST['cost-' . $cost->id . '-description'];
                                            $cost->lang = $_SESSION['translate_lang'];
                                            $cost->project = $project->id;
                                            $cost->saveLang($errors);
                                        }
                                    }
                                }
                                break;

                            case 'rewards':
                                if ($action == 'save') {
                                    foreach ($project->social_rewards as $k => $reward) {
                                        if (isset($_POST['social_reward-' . $reward->id . '-reward'])) {
                                            $reward->reward_lang = $_POST['social_reward-' . $reward->id . '-reward'];
                                            $reward->description_lang = $_POST['social_reward-' . $reward->id . '-description'];
                                            $reward->other_lang = $_POST['social_reward-' . $reward->id . '-other'];
                                            $reward->lang = $_SESSION['translate_lang'];
                                            $reward->project = $project->id;
                                            $reward->saveLang($errors);
                                        }
                                    }
                                    foreach ($project->individual_rewards as $k => $reward) {
                                        if (isset($_POST['individual_reward-' . $reward->id . '-reward'])) {
                                            $reward->reward_lang = $_POST['individual_reward-' . $reward->id . '-reward'];
                                            $reward->description_lang = $_POST['individual_reward-' . $reward->id . '-description'];
                                            $reward->other_lang = $_POST['individual_reward-' . $reward->id . '-other'];
                                            $reward->lang = $_SESSION['translate_lang'];
                                            $reward->project = $project->id;
                                            $reward->saveLang($errors);
                                        }
                                    }
                                }
                                break;

                            case 'supports':
                                if ($action == 'save') {
                                    // tratar colaboraciones existentes
                                    foreach ($project->supports as $key => $support) {
                                        if (isset($_POST['support-' . $support->id . '-support'])) {
                                            // guardamos los datos traducidos
                                            $support->support_lang = $_POST['support-' . $support->id . '-support'];
                                            $support->description_lang = $_POST['support-' . $support->id . '-description'];
                                            $support->lang = $_SESSION['translate_lang'];
                                            $support->project = $project->id;
                                            $support->saveLang($errors);

                                            // actualizar el Mensaje correspondiente, solamente actualizar
                                            $msg = Model\Message::get($support->thread);
                                            $msg->message_lang = "{$support->support_lang}: {$support->description_lang}";
                                            $msg->lang = $_SESSION['translate_lang'];
                                            $msg->saveLang($errors);
                                        }
                                    }
                                }
                                break;

                            case 'updates':
                                if (empty($_POST['blog']) || empty($_POST['id'])) {
                                    break;
                                }

                                $post = Model\Blog\Post::get($_POST['id']);

                                $post->title_lang = $_POST['title'];
                                $post->text_lang = $_POST['text'];
                                $post->media_lang = $_POST['media'];
                                $post->legend_lang = $_POST['legend'];
                                $post->lang = $_SESSION['translate_lang'];
                                $post->saveLang($errors);

                                $action = 'edit';
                                break;
                        }
                    }

                    switch ($option) {
                        case 'profile':
                            $viewData['user'] = Model\User::get($project->owner, $_SESSION['translate_lang']);
                            break;

                        case 'overview':
                            break;

                        case 'costs':
                            if ($_POST) {
                                foreach ($_POST as $k => $v) {
                                    if (!empty($v) && preg_match('/cost-(\d+)-edit/', $k, $r)) {
                                        $viewData[$k] = true;
                                    }
                                }
                            }
                            break;

                        case 'rewards':
                            if ($_POST) {
                                foreach ($_POST as $k => $v) {
                                    if (!empty($v) && preg_match('/((social)|(individual))_reward-(\d+)-edit/', $k)) {
                                        $viewData[$k] = true;
                                        break;
                                    }
                                }
                            }
                            break;

                        case 'supports':
                            if ($_POST) {
                                foreach ($_POST as $k => $v) {
                                    if (!empty($v) && preg_match('/support-(\d+)-edit/', $k, $r)) {
                                        $viewData[$k] = true;
                                        break;
                                    }
                                }
                            }
                            break;

                        // publicar actualizaciones
                        case 'updates':
                            $viewData['blog'] = $blog;

                            if ($action == 'edit') {
                                $post = Model\Blog\Post::get($id, $_SESSION['translate_lang']);
                                $viewData['post'] = $post;
                            } else {
                                $posts = array();
                                foreach ($blog->posts as $post) {
                                    $posts[] = Model\Blog\Post::get($post->id, $_SESSION['translate_lang']);
                                }
                                $viewData['posts'] = $posts;
                            }
                            break;
                    }

                    $viewData['project'] = $project;
                    $viewData['original'] = $project_original;
//// FIN Control de traduccion de proyecto
                    break;

                case 'call':
                    try {
                        // si lo tenemos en sesion una id de convocatoria
                        if ( isset($_SESSION['translate_call']) ) {

                            $id = (is_object($_SESSION['translate_call'])) ? $_SESSION['translate_call']->id : $_SESSION['translate_call'];

                            // instancia original
                            $call_original = Model\Call::get($id, null); // $lang = null para sacar idioma original

                            // instancia traducción
                            $call = Model\Call::get($id, $_SESSION['translate_lang']);

                        } else {

                            // no tenemos convocatoria
                            $call_original = null;
                            $call = null;
                        }

                    } catch (\Goteo\Core\Error $e) {
                        Message::Error('Ha fallado al cargar los datos de la convocatoria');
                        $_SESSION['translate_type'] = 'profile';
                        throw new Redirection('/dashboard/translates');
                    }

                    if (!$call instanceof Model\Call || !$call_original instanceof Model\Call) {
                        Message::Error('Ha fallado al cargar los datos de la convocatoria');
                        $_SESSION['translate_type'] = 'profile';
                        throw new Redirection('/dashboard/translates');
                    }

                    $_SESSION['translate_call'] = $call;
                    $call_original->lang_name = $langs[$call_original->lang]->name;

                    // quitamos el idioma original del desplegable de idiomas
                    unset($viewData['langs'][$call_original->lang]);

//// Control de traduccion de convocatoria
                    // tratar lo que llega por post para guardar los datos
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        switch ($option) {
                            case 'profile':
                                if ($action == 'save') {
                                    $user = Model\User::get($_POST['id'], $_SESSION['translate_lang']);
                                    $user->about_lang = $_POST['about'];
                                    $user->keywords_lang = $_POST['keywords'];
                                    $user->contribution_lang = $_POST['contribution'];
                                    $user->lang = $_SESSION['translate_lang'];
                                    $user->saveLang($errors);
                                }
                                break;

                            case 'overview':
                                if ($action == 'save') {
                                    $call->name_lang = $_POST['name'];
                                    $call->description_lang = $_POST['description'];
                                    $call->whom_lang = $_POST['whom'];
                                    $call->apply_lang = $_POST['apply'];
                                    $call->legal_lang = $_POST['legal'];
                                    $call->dossier_lang = $_POST['dossier'];
                                    $call->tweet_lang = $_POST['tweet'];
                                    $call->resources_lang = $_POST['resources'];
                                    $call->subtitle_lang = $_POST['subtitle'];
                                    $call->lang_lang = $_SESSION['translate_lang'];
                                    $call->saveLang($errors);
                                }
                                break;

                            case 'banners':
                                if ($action == 'save') {
                                    // tratar colaboraciones existentes
                                    foreach ($call->banners as $key => $banner) {
                                        if (isset($_POST['banner-' . $banner->id . '-name'])) {
                                            // guardamos los datos traducidos
                                            $banner->name_lang = $_POST['banner-' . $banner->id . '-name'];
                                            $banner->lang = $_SESSION['translate_lang'];
                                            $banner->saveLang($errors);
                                        }
                                    }
                                }
                                break;

                        }
                    }

                    if ($option == 'profile') {
                        $viewData['user'] = Model\User::get($call->owner, $_SESSION['translate_lang']);
                    }

                    if ($option == 'overview') {
                        $viewData['option'] = 'call_overview';
                    }

                    if ($option == 'banners') {
                        $viewData['option'] = 'call_banners';
                        if ($_POST) {
                            foreach ($_POST as $k => $v) {
                                if (!empty($v) && preg_match('/banner-(\d+)-edit/', $k, $r)) {
                                    $viewData[$k] = true;
                                    break;
                                }
                            }
                        }
                    }


                    $viewData['call'] = $call;
                    $viewData['original'] = $call_original;
//// FIN Control de traduccion de convocatoria
                    break;

                case 'node':
                    try {
                        // si lo tenemos en sesion una id de convocatoria
                        if ( isset($_SESSION['translate_node']) ) {

                            $id = (is_object($_SESSION['translate_node'])) ? $_SESSION['translate_node']->id : $_SESSION['translate_node'];

                            // instancia original
                            $node_original = Model\Node::get($id, null); // $lang = null para sacar idioma original

                            // instancia traducción
                            $node = Model\Node::get($id, $_SESSION['translate_lang']);

                        } else {

                            // no tenemos convocatoria
                            $node_original = null;
                            $node = null;
                        }

                    } catch (\Goteo\Core\Error $e) {
                        Message::Error('Ha fallado al cargar los datos del nodo');
                        $_SESSION['translate_type'] = 'profile';
                        throw new Redirection('/dashboard/translates');
                    }

                    if (!$node instanceof Model\Node || !$node_original instanceof Model\Node) {
                        Message::Error('Ha fallado al cargar los datos del nodo');
                        $_SESSION['translate_type'] = 'profile';
                        throw new Redirection('/dashboard/translates');
                    }

                    $_SESSION['translate_node'] = $node;

                    // el original del nodo siempre es en español (?¿)
                    $node_original->lang_name = $langs['es']->name;
                    unset($viewData['langs']['es']); // quitamos el idioma original
                    // la traducción de contenidos se hace en /traslate/node/list/id-nodo

                    $viewData['option'] = 'node_overview';
                    $viewData['node'] = $node;
                    $viewData['original'] = $node_original;
                    break;
                default: // profile
                    $viewData['option'] = 'profile';
                    unset($langs['es']);

                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        if ($action == 'save') {
                            $user = Model\User::get($_POST['id'], $_SESSION['translate_lang']);
                            $user->about_lang = $_POST['about'];
                            $user->keywords_lang = $_POST['keywords'];
                            $user->contribution_lang = $_POST['contribution'];
                            $user->lang = $_SESSION['translate_lang'];
                            $user->saveLang($errors);
                        }
                    }

                    $viewData['user'] = Model\User::get($user->id, $_SESSION['translate_lang']);
                    $viewData['original'] = Model\User::get($user->id);
            }

            if (!empty($errors)) {
                Message::Error('HA HABIDO ERRORES: <br />' . implode('<br />', $errors));
            }

            return new View('dashboard/index.html.php', $viewData);
        }

        /*
         * Seccion, Mis convocatorias
         * Opciones:
         *      'proyectos' visualización de los proyectos que tienen capital riego de la convocatoria
         *
         */

        public function calls($option = 'summary', $action = 'list', $id = null) {

            $user = $_SESSION['user'];

            $errors = array();

            list($call, $calls) = Dashboard\Calls::verifyCalls($user, $action);

            // view data basico para esta seccion
            $viewData = array(
                'menu' => self::menu(),
                'section' => __FUNCTION__,
                'option' => $option,
                'action' => $action,
                'calls' => $calls
            );


            switch ($option) {
                // proyectos
                case 'projects':

                    /*
                     * Ya no hay Modo Asignar proyectos
                     * y tampoco permitimos desasignar
                     *
                    if ($action == 'assign_mode' && $id == 'on') {
                        if ($call->status < 3) {
                            $_SESSION['assign_mode'] = true;
                            throw new Redirection('/discover/call');
                        } else {
                            Message::Error('No se pueden asignar proyectos ahora');
                            unset($_SESSION['assign_mode']);
                        }
                    } elseif ($action == 'assign_mode' && $id == 'off') {
                        unset($_SESSION['assign_mode']);
                        throw new Redirection('/dashboard/calls/summary');
                    }

                    //si estamos quitando un proyecto
                    if ($action == 'unassign' && !empty($id) && isset($call->projects[$id]) && $call->projects[$id]->amount <= 0) {

                        $registry = new Model\Call\Project;
                        $registry->id = $id;
                        $registry->call = $call->id;
                        if ($registry->remove($errors)) {
                            Message::Error('El proyecto se ha quitado correctamente de la convocatoria');
                            $call->projects = Model\Call\Project::get($call->id);
                        } else {
                            Message::Error('Falló al quitar el proyecto: ' . implode('<br />', $errors));
                        }
                    } elseif ($action == 'unassign') {
                        Message::Error('No se puede quitar este proyecto ahora');
                    }
                    */

                    break;

                // patrocinadores
                case 'sponsors':
                    $viewData['sponsor'] = Dashboard\Calls::process_sponsors($id, $call->id, $action, $errors);
                    break;
            }

            $viewData['errors'] = $errors;
            $viewData['call'] = $call;

            return new View('dashboard/index.html.php', $viewData);
        }

        private static function menu() {
            // todos los textos del menu dashboard
            $menu = array(
                'activity' => array(
                    'label' => Text::get('dashboard-menu-activity'),
                    'options' => array(
                        'summary' => Text::get('dashboard-menu-activity-summary'),
                        'donor' => Text::get('dashboard-menu-activity-donor')
                    )
                ),
                'profile' => array(
                    'label' => Text::get('dashboard-menu-profile'),
                    'options' => array(
                        'profile' => Text::get('dashboard-menu-profile-profile'),
                        'personal' => Text::get('dashboard-menu-profile-personal'),
                        'location' => Text::get('dashboard-menu-profile-location'),
                        'access' => Text::get('dashboard-menu-profile-access'),
                        'preferences' => Text::get('dashboard-menu-profile-preferences'),
                        'public' => Text::get('dashboard-menu-profile-public')
                    )
                ),
                'projects' => array(
                    'label' => Text::get('dashboard-menu-projects'),
                    'options' => array(
                        'summary' => Text::get('dashboard-menu-projects-summary'),
                        'updates' => Text::get('dashboard-menu-projects-updates'),
                        'supports' => Text::get('dashboard-menu-projects-supports'),
                        'rewards' => Text::get('dashboard-menu-projects-rewards'),
                        'messengers' => Text::get('dashboard-menu-projects-messegers'),
                        'contract' => Text::get('dashboard-menu-projects-contract'),
                        'commons' => Text::get('dashboard-menu-projects-commons')
                    )
                )
            );

            // si es un convocador
            if (isset($_SESSION['user']->roles['caller'])) {
                $menu['calls'] = array(
                    'label' => Text::get('dashboard-menu-calls'),
                    'options' => array(
                        'summary' => Text::get('dashboard-menu-calls-summary'),
//                        'projects' => Text::get('dashboard-menu-calls-projects'),
//                        'sponsors' => Text::get('dashboard-menu-calls-sponsors')
                    )
                );
            }

            // segun lo que este traduciendo
            if ($_SESSION['translate_type'] == 'project') {
                // si esta traduciendo un proyecto
                $menu['translates'] = array(
                    'label' => Text::get('dashboard-menu-translates'),
                    'options' => array(
                        'profile' => Text::get('step-1'),
                        'overview' => Text::get('step-3'),
                        'costs' => Text::get('step-4'),
                        'rewards' => Text::get('step-5'),
                        'supports' => Text::get('step-6'),
                        'updates' => Text::get('project-menu-updates')
                    )
                );
            } elseif ($_SESSION['translate_type'] == 'call') {
                // si esta traduciendo una convocatoria
                $menu['translates'] = array(
                    'label' => Text::get('dashboard-menu-translates'),
                    'options' => array(
                        'profile' => Text::get('step-1'),
                        'overview' => Text::get('step-3'),
                        'banners' => 'Banners'
                    )
                );
            } elseif ($_SESSION['translate_type'] == 'node') {
                // si esta traduciendo un nodo
                $menu['translates'] = array(
                    'label' => Text::get('dashboard-menu-translates'),
                    'options' => array(
                        'overview' => 'Contenidos de nodo'
                    )
                );
            } else {
                // si está traduciendo su perfil
                $menu['translates'] = array(
                    'label' => Text::get('dashboard-menu-translates'),
                    'options' => array(
                        'profile' => Text::get('step-1')
                    )
                );
            }

            // si tiene algún rol de admin
            if ( isset($_SESSION['user']->roles['admin']) || isset($_SESSION['user']->roles['superadmin']) )
                $menu['activity']['options']['admin'] = Text::get('dashboard-menu-admin_board');

            // si tiene rol de revisor
            if ( isset($_SESSION['user']->roles['checker']) )
                $menu['activity']['options']['review'] = Text::get('dashboard-menu-review_board');

            // si tiene rol de traductor
            if ( isset($_SESSION['user']->roles['admin']) || isset($_SESSION['user']->roles['translator']) )
                $menu['activity']['options']['translate'] = Text::get('dashboard-menu-translate_board');

            return $menu;
        }

    }

}
