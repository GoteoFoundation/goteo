<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Library\Text,
        Goteo\Library\Mail,
        Goteo\Library\Template,
        Goteo\Library\Message,
        Goteo\Library\Feed,
        Goteo\Model;

    class Call extends \Goteo\Core\Controller {

        public function index($id, $show = 'splash') {
            if ($id !== null) {
                if ($show == 'apply') {
                    // Preparamos la sesión para que al crear proyecto se asigne a esta convocatoria
                    $this->apply($id);
                } else {
                    return $this->view($id, $show);
                }
            } else if (isset($_GET['create'])) {
                throw new Redirection("/call/create");
            } else {
                throw new Redirection("/");
            }
        }

        public function raw ($id) {
            $call = Model\Call::get($id, LANG);
            \trace($call);
            die;
        }

        public function delete ($id) {
            $call = Model\Call::get($id);
            if ($call->delete()) {
                if ($_SESSION['call']->id == $id) {
                    unset($_SESSION['call']);
                }
            }
            throw new Redirection("/dashboard/calls");
        }

        //Aunque no esté en estado edición un admin siempre podrá editar un proyecto
        public function edit ($id) {
            $call = Model\Call::get($id, null);

            // si no tenemos SESSION stepped es porque no venimos del create
            if (!isset($_SESSION['stepped']))
                $_SESSION['stepped'] = array(
                     'userProfile'  => 'userProfile',
                     'userPersonal' => 'userPersonal',
                     'overview'     => 'overview'
                );

            // aqui uno que pueda entrar a editar siempre puede ir a todos los pasos
            if ($call->status != 1 && !ACL::check('/call/edit/todos')) {
                // solo puede estar en preview
                $step = 'preview';

                $steps = array(
                    'preview' => array(
                        'name' => Text::get('step-7'),
                        'title' => Text::get('step-preview'),
                        'offtopic' => true
                    )
                );


            } else {
                // todos los pasos, entrando en userProfile por defecto
                $step = 'userProfile';

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

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $errors = array(); // errores al procesar, no son errores en los datos del proyecto
                foreach ($steps as $id => &$data) {
                    
                    if (call_user_func_array(array($this, "process_{$id}"), array(&$call, &$errors))) {
                        // si un process devuelve true es que han enviado datos de este paso, lo añadimos a los pasados
                        if (!in_array($id, $_SESSION['stepped'])) {
                            $_SESSION['stepped'][$id] = $id;
                        }
                    }
                    
                }

                // guardamos los datos que hemos tratado y los errores de los datos
                $call->save($errors);

                // aqui no se manda a revision, se da por terminada la edicion
                if (isset($_POST['process_preview']) && !empty($_POST['finish'])) {
                    $errors = array();
                    if ($call->ready($errors)) {

                        // email a los de goteo
                        $mailHandler = new Mail();

                        $mailHandler->to = \GOTEO_MAIL;
                        $mailHandler->toName = 'Revisor de convocatorias';
                        $mailHandler->subject = 'Convocatoria ' . $call->name . ' finalizó la edición';
                        $mailHandler->content = '<p>Se ha finalizado la edicion de la convocatoria <span class="message-highlight-blue">'.$call->name.'</span>, se puede ver en <span class="message-highlight-blue"><a href="'.SITE_URL.'/call/'.$call->id.'">'.SITE_URL.'/call/'.$call->id.'</a></span></p>';
                        $mailHandler->fromName = "{$call->user->name}";
                        $mailHandler->from = $call->user->email;
                        $mailHandler->html = true;
                        $mailHandler->template = 0;
                        if ($mailHandler->send($errors)) {
                            Message::Info(Text::get('call-review-request_mail-success'));
                        } else {
                            Message::Error(Text::get('call-review-request_mail-fail'));
                            Message::Error(implode('<br />', $errors));
                        }

                        unset($mailHandler);

                        // email al dueño
                        $mailHandler = new Mail();

                        $mailHandler->to = $call->user->email;
                        $mailHandler->toName = $call->user->name;
                        $mailHandler->subject = 'Convocatoria ' . $call->name . ' creada correctamente';
                        $mailHandler->content = '<p>Se ha completado la creación de la convocatoria <span class="message-highlight-blue">'.$call->name.'</span>, ya se puedes seleccionar proyectos.</p>';
                        $mailHandler->html = true;
                        $mailHandler->template = 0;
                        if ($mailHandler->send($errors)) {
                            Message::Info(Text::get('call-review-confirm_mail-success'));
                        } else {
                            Message::Error(Text::get('call-review-confirm_mail-fail'));
                            Message::Error(implode('<br />', $errors));
                        }

                        unset($mailHandler);

                        /*
                         * Evento Feed
                         */
                        $log = new Feed();
                        $log->title = 'convocatoria enviada a revision';
                        $log->url = '/admin/calls';
                        $log->type = 'project';
                        $log_text = '%s ha completado la edición de la convocatoria %s, se dispone a <span class="red">asignar proyectos</span>';
                        $log_items = array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('call', $call->name, $call->id)
                        );
                        $log->html = \vsprintf($log_text, $log_items);
                        $log->add($errors);
                        unset($log);

                        if ($_SESSION['user']->id == $call->ower) {
                            $_SESSION['call'] = $call->id;
                            throw new Redirection("/dashboard/calls/projects");
                        } else {
                            throw new Redirection("/admin/calls/projects/{$call->id}");
                        }
                    }
                }


            }

            //re-evaluar los errores
            $call->check();
            
            // variables para la vista
            $viewData = array(
                'call' => $call,
                'steps' => $steps,
                'step' => $step
            );


            // segun el paso añadimos los datos auxiliares para pintar
            switch ($step) {
                case 'userProfile':
                    $owner = Model\User::get($call->owner);
                    // si es el avatar por defecto no lo mostramos aqui
                    if ($owner->avatar->id == 1) {
                        unset($owner->avatar);
                    }
                    $viewData['user'] = $owner;
                    $viewData['interests'] = Model\User\Interest::getAll();

                    if ($_POST) {
                        foreach ($_POST as $k => $v) {
                            if (!empty($v) && preg_match('/web-(\d+)-edit/', $k, $r)) {
                                $viewData[$k] = true;
                            }
                        }

                        if (!empty($_POST['web-add'])) {
                            $last = end($owner->webs);
                            if ($last !== false) {
                                $viewData["web-{$last->id}-edit"] = true;
                            }
                        }
                    }
                    break;
                
                case 'overview':
                    $viewData['categories'] = Model\Call\Category::getAll();
                    $viewData['icons'] = Model\Icon::getAll();
                    break;

                case 'preview':
                    break;
            }


            $view = new View (
                "view/call/edit.html.php",
                $viewData
            );

            return $view;

        }

        public function create () {

            $error = false;

            if (empty($_SESSION['user'])) {
                $_SESSION['jumpto'] = '/call/create';
                Message::Info(Text::get('user-login-required-to_create'));
                throw new Redirection("/user/login");
            } elseif ($_POST['action'] != 'continue' || $_POST['confirm'] != 'true') {
                $error = true;
            } elseif (empty($_POST['name'])) {
                Message::Error('Falta identificador');
                $error = true;
            } elseif (isset($_POST['admin']) && empty($_POST['caller'])) {
                Message::Error('Falta convocador');
                $error = true;
            } else {
                $name = $_POST['name'];
                $caller = empty($_POST['caller']) ? $_SESSION['user']->id : $_POST['caller'];

                $errors = array();

                // deberiamos poder crearla
                $call = new Model\Call;
                if ($call->create($name, $caller, $errors)) {
                    $_SESSION['stepped'] = array();

                    // permisos para editarlo y borrarlo
                    ACL::allow('/call/edit/'.$call->id, '*', 'caller', $_SESSION['user']->id);
                    ACL::allow('/call/delete/'.$call->id, '*', 'caller', $_SESSION['user']->id);

                    /*
                     * Evento Feed
                     */
                    $log = new Feed();
                    $log->title = 'usuario crea nueva convocatoria';
                    $log->url = 'admin/calls';
                    $log->type = 'call';
                    $log_text = '%s ha creado una nueva convocatoria, %s';
                    $log_items = array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('call', $call->name, $call->id)
                    );
                    $log->html = \vsprintf($log_text, $log_items);
                    $log->add($errors);
                    unset($log);

                } else {
                    Message::Error('No se ha podido crear la convocatoria,  intente otro identificador');
                    $error = true;
                }

            }

            if ($error) {
                if (isset($_POST['admin'])) {
                    throw new Redirection("/admin/calls/add");
                } else {
                    throw new Redirection("/about/call");
                }
            } else {
                throw new Redirection("/call/edit/{$call->id}");
            }

        }

        private function view ($id, $show) {
            $call = Model\Call::get($id, LANG);

            if (!$call instanceof Model\Call) {
                Message::Error('Ha habido algun errror al cargar la convocatoria solicitada');
                throw new Redirection("/");
            }

            // solamente se puede ver publicamente si
            // - es el dueño
            // - es un admin con permiso
            // - es otro usuario y el proyecto esta available: en aceptacion, en campaña, financiado
            if (($call->status > 2) ||
                $call->owner == $_SESSION['user']->id ||
                ACL::check('/call/edit/todos') ||
                ACL::check('/call/view/todos')) {

                if (!\in_array($show, array('splash'))) {
                    $show = 'index';
                }

                $call->categories = Model\Call\Category::getNames($call->id);
                $call->icons = Model\Call\Icon::getNames($call->id);

                // lo puede ver
                return new View('view/call/'.$show.'.html.php', array('call' => $call));

            } else {
                // no lo puede ver
                throw new Redirection("/");
            }
        }

        private function apply ($id) {
            $_SESSION['oncreate_applyto'] = $id;
            throw new Redirection("/project/create");
        }


        //-----------------------------------------------
        // Métodos privados para el tratamiento de datos
        // del save y remove de las tablas relacionadas se enmcarga el model/call
        // primero añadir y luego quitar para que no se pisen los indices
        // En vez del hidden step, va a comprobar que esté definido en el post el primer campo del proceso
        //-----------------------------------------------
        /*
         * Paso 1 - PERFIL
         */
        private function process_userProfile(&$call, &$errors) {
            if (!isset($_POST['process_userProfile'])) {
                return false;
            }

            $user = Model\User::get($call->owner);

            // tratar la imagen y ponerla en la propiedad avatar
            // __FILES__

            $fields = array(
                'user_name'=>'name',
                'user_location'=>'location',
                'user_avatar'=>'avatar',
                'user_about'=>'about',
                'user_keywords'=>'keywords',
                'user_contribution'=>'contribution',
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
            if(!empty($_FILES['avatar_upload']['name'])) {
                $user->avatar = $_FILES['avatar_upload'];
            }

            // tratar si quitan la imagen
            if (!empty($_POST['avatar-' . $user->avatar->id .  '-remove'])) {
                $user->avatar->remove('user');
                $user->avatar = '';
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
            $user->save($call->errors['userProfile']);
            $user = Model\User::flush();
            return true;
        }

        /*
         * Paso 2 - DATOS PERSONALES
         */
        private function process_userPersonal(&$call, &$errors) {
            if (!isset($_POST['process_userPersonal'])) {
                return false;
            }

            // campos que guarda este paso
            $fields = array(
                'contract_name',
                'contract_nif',
                'contract_email',
                'phone',
                'contract_entity',
                'contract_birthdate',
                'entity_office',
                'entity_name',
                'entity_cif',
                'address',
                'zipcode',
                'location',
                'country',
                'secondary_address',
                'post_address',
                'post_zipcode',
                'post_location',
                'post_country'
            );

            $personalData = array();

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $call->$field = $_POST[$field];
                    $personalData[$field] = $_POST[$field];
                }
            }

            if (!$_POST['secondary_address']) {
                $call->post_address = null;
                $call->post_zipcode = null;
                $call->post_location = null;
                $call->post_country = null;
            }

            // actualizamos estos datos en los personales del usuario
            if (!empty ($personalData)) {
                Model\User::setPersonal($call->owner, $personalData, true);
            }

            return true;
        }

        /*
         * Paso 3 - DESCRIPCIÓN
         *
         * Diferente de proyecto
         *
         *
         */

        private function process_overview(&$call, &$errors) {
            if (!isset($_POST['process_overview'])) {
                return false;
            }

            // campos que guarda este paso
            // image, media y category  van aparte
            $fields = array(
                'name',
                'subtitle',
                'description',
                'whom',
                'apply',
                'legal',
                'dossier',
                'call_location',
                'amount',
                'days'
            );

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $call->$field = $_POST[$field];
                }
            }

            // Logo e imagen de fondo
            // tratar si quitan el logo
            if (!empty($_POST['logo-' . $call->logo .  '-remove'])) {
                $logo = Model\Image::get($call->logo);
                $logo->remove();
                $call->logo = '';
            }

            // tratar el logo que suben
            if(!empty($_FILES['logo_upload']['name'])) {
                $call->logo = $_FILES['logo_upload'];
            }

            // tratar si quitan la imagen
            if (!empty($_POST['image-' . $call->image .  '-remove'])) {
                $image = Model\Image::get($call->image);
                $image->remove();
                $call->image = '';
            }

            // tratar la imagen que suben
            if(!empty($_FILES['image_upload']['name'])) {
                $call->image = $_FILES['image_upload'];
            }
            
            //categorias
            // añadir las que vienen y no tiene
            $tiene = $call->categories;
            if (isset($_POST['categories'])) {
                $viene = $_POST['categories'];
                $quita = array_diff($tiene, $viene);
            } else {
                $quita = $tiene;
            }
            $guarda = array_diff($viene, $tiene);
            foreach ($guarda as $key=>$cat) {
                $category = new Model\Call\Category(array('id'=>$cat,'call'=>$call->id));
                $call->categories[] = $category;
            }

            // quitar las que tiene y no vienen
            foreach ($quita as $key=>$cat) {
                unset($call->categories[$key]);
            }

            // iconos
            // añadir los que vienen y no tiene
            $tiene = $call->icons;
            if (isset($_POST['icons'])) {
                $viene = $_POST['icons'];
                $quita = array_diff($tiene, $viene);
            } else {
                $quita = $tiene;
            }
            $guarda = array_diff($viene, $tiene);
            foreach ($guarda as $key=>$ico) {
                $icon = new Model\Call\Icon(array('id'=>$ico,'call'=>$call->id));
                $call->icons[] = $icon;
            }

            // quitar las que tiene y no vienen
            foreach ($quita as $key=>$ico) {
                unset($call->icons[$key]);
            }

            return true;
        }

        /*
         * Paso 4 - PREVIEW
         * No hay nada que tratar porque aq este paso no se le envia nada por post
         */
        private function process_preview(&$call) {
            if (!isset($_POST['process_preview'])) {
                return false;
            }

            return true;
        }
        //-------------------------------------------------------------
        // Hasta aquí los métodos privados para el tratamiento de datos
        //-------------------------------------------------------------
   }

}