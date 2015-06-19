<?php

namespace Goteo\Controller {

    use Goteo\Core\ACL,
        Goteo\Core\Error,
        Goteo\Core\Redirection,
        Goteo\Core\View,
        Goteo\Library\Text,
        Goteo\Application,
        Goteo\Application\Lang,
        Goteo\Library\Template,
        Goteo\Library\Feed,
        Goteo\Library\Buzz,
        Goteo\Model;

    class Call extends \Goteo\Core\Controller {

        public function index($id, $show = 'index') {
            if ($id !== null) {

                //#TODO:  publicada en aplicación cerrada, pasará a configuración
//                define('CALL_NOAPPLY', true);
//                if ($show == 'apply') $show = 'info';


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
            $call = Model\Call::get($id, Lang::current());
            \trace($call);
            die;
        }

        public function delete ($id) {
            // redirección según usuario
            $goto = isset($_SESSION['user']->roles['admin']) ? '/admin/calls' : '/dashboard/projects';

            $call = Model\Call::getMini($id);

            // no lo puede eliminar si
            $grant = false;
            if ($call->owner == $_SESSION['user']->id // es el convocador
                || (isset($_SESSION['admin_node']) && $_SESSION['admin_node'] == \GOTEO_NODE) // es admin de central
                || isset($_SESSION['user']->roles['superadmin']) // es superadmin
            )
                $grant = true;

            if (!$grant) {
                Application\Message::info('No tienes permiso para eliminar esta convocatoria');

                throw new Redirection($goto);
            }


            if ($call->delete()) {
                if ($_SESSION['call']->id == $id) {
                    unset($_SESSION['call']);
                }
            }
            throw new Redirection("/dashboard/calls");
        }

        /**
         * Formulario edición de convocatoria
         *
         * @param $id  Identificador de la convocatoria
         * @param string $step  Paso que se cargará si se especifica en la url /call/-id-/edit/-Step-
         * @return View ( /view/call/edit/view.html.php )
         * @throws \Goteo\Core\Redirection (si es un acceso no permitido)
         */
        public function edit ($id, $step = 'userProfile') {
            // redirección según usuario
            $goto = isset($_SESSION['user']->roles['admin']) ? '/admin/calls' : '/dashboard/projects';

            $call = Model\Call::get($id, null);

            $grant = false;
            // Substituye ACL, solo lo puede editar si...
            if ($call->owner == $_SESSION['user']->id // es su proyecto
                || (isset($_SESSION['admin_node']) && $_SESSION['admin_node'] == \GOTEO_NODE) // es admin de central
                || $call->isAdmin($_SESSION['user']->id) // la tiene asignada
                || isset($_SESSION['user']->roles['superadmin']) // es superadmin
            )
                $grant = true;

            if (!$grant) {
                Application\Message::info('No tienes permiso para editar esta convocatoria');
                throw new Redirection($goto);
            }


            if (isset($_GET['from']) && $_GET['from'] == 'dashboard') {
                // Evento Feed
                $log = new Feed();
                $log->setTarget($call->id, 'call');
                $log->populate('El convocador entra a editar desde dashboard', '/admin/calls/'.$id,
                    \vsprintf('El convocador %s ha entrado a editar su convocatoria %s desde su dashboard', array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('call', $call->name, $call->id)
                )));
                $log->doAdmin('call');
                unset($log);
            }

            // aqui uno que pueda entrar a editar siempre puede ir a todos los pasos
            // excepto el autor si ya no está en edición
            if ($call->status != 1 && $call->owner == $_SESSION['user']->id) {
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
                // todos los pasos
                // entrando, por defecto, en el paso especificado en url
                $steps = array(
                    'userProfile' => array(
                        'name' => Text::get('step-1'),
                        'title' => Text::get('step-userProfile'),
                        'offtopic' => true
                    ),
                    /* 'userPersonal' => array(
                        'name' => Text::get('step-2'),
                        'title' => Text::get('step-userPersonal'),
                        'offtopic' => true
                    ), */
                    'overview' => array(
                        'name' => Text::get('step-3'),
                        'title' => Text::get('step-overview')
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

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $errors = array(); // errores al procesar, no son errores en los datos del proyecto
                foreach ($steps as $id => &$data) {

                    if (call_user_func_array(array($this, "process_{$id}"), array(&$call, &$errors))) {
                        // ok... nada más
                    }

                }

                // guardamos los datos que hemos tratado y los errores de los datos
                $call->save($errors);

                // aqui no se manda a revision, se da por terminada la edicion
                if (isset($_POST['process_preview']) && !empty($_POST['finish'])) {
                    $errors = array();
                    if ($call->ready($errors)) {

                        // email a los de goteo
                        $mailHandler = new Library\Mail();

                        $mailHandler->to = \GOTEO_MAIL;
                        $mailHandler->toName = 'Revisor de convocatorias';
                        $mailHandler->subject = 'Convocatoria ' . $call->name . ' finalizó la edición';
                        $mailHandler->content = '<p>Se ha finalizado la edicion de la convocatoria <span class="message-highlight-blue">'.$call->name.'</span>, se puede ver en <span class="message-highlight-blue"><a href="'.SITE_URL.'/call/'.$call->id.'">'.SITE_URL.'/call/'.$call->id.'</a></span></p>';
                        $mailHandler->reply = $call->user->email;
                        $mailHandler->replyName = "{$call->user->name}";
                        $mailHandler->html = true;
                        $mailHandler->template = 0;
                        if ($mailHandler->send($errors)) {
                            Application\Message::info(Text::get('call-review-request_mail-success'));
                        } else {
                            Application\Message::error(Text::get('call-review-request_mail-fail'));
                            Application\Message::error(implode('<br />', $errors));
                        }

                        unset($mailHandler);

                        // email al dueño
                        $mailHandler = new Library\Mail();

                        $mailHandler->to = $call->user->email;
                        $mailHandler->toName = $call->user->name;
                        $mailHandler->subject = 'Convocatoria ' . $call->name . ' creada correctamente';
                        $mailHandler->content = '<p>Se ha completado la creación de la convocatoria <span class="message-highlight-blue">'.$call->name.'</span>, ya se puedes seleccionar proyectos.</p>';
                        $mailHandler->html = true;
                        $mailHandler->template = 0;
                        if ($mailHandler->send($errors)) {
                            Application\Message::info(Text::get('call-review-confirm_mail-success'));
                        } else {
                            Application\Message::error(Text::get('call-review-confirm_mail-fail'));
                            Application\Message::error(implode('<br />', $errors));
                        }

                        unset($mailHandler);

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($call->id, 'call');
                        $log->populate('convocatoria enviada a revision', '/admin/calls',
                            \vsprintf('%s ha completado la edición de la convocatoria %s, se dispone a <span class="red">asignar proyectos</span>', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('call', $call->name, $call->id)
                        )));
                        $log->doAdmin('project');
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
                    // TODO: esto en la vista!
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
                    $viewData['scope'] = Model\Project::scope();
                    break;

                case 'supports':
                    if ($_POST) {
                        foreach ($_POST as $k => $v) {
                            if (!empty($v) && preg_match('/banner-(\d+)-edit/', $k, $r)) {
                                $viewData[$k] = true;
                            }
                        }

                        if (!empty($_POST['banner-add'])) {
                            $last = end($call->banners);
                            if ($last !== false) {
                                $viewData["banner-{$last->id}-edit"] = true;
                            }
                        }

                        foreach ($_POST as $k => $v) {
                            if (!empty($v) && preg_match('/sponsor-(\d+)-edit/', $k, $r)) {
                                $viewData[$k] = true;
                            }
                        }

                        if (!empty($_POST['sponsor-add'])) {
                            $last = end($call->sponsors);
                            if ($last !== false) {
                                $viewData["sponsor-{$last->id}-edit"] = true;
                            }
                        }
                    }
                    break;

                case 'preview':
                    break;
            }

            $view = new View (
                'call/edit.html.php',
                $viewData
            );

            return $view;

        }

        public function create () {

            $error = false;

            if (empty($_SESSION['user'])) {
                $_SESSION['jumpto'] = '/call/create';
                Application\Message::info(Text::get('user-login-required-to_create'));
                throw new Redirection(SEC_URL."/user/login");
            } elseif ($_POST['action'] != 'continue' || $_POST['confirm'] != 'true') {
                $error = true;
            } elseif (empty($_POST['name'])) {
                Application\Message::error('Falta identificador');
                $error = true;
            } elseif (isset($_POST['admin']) && empty($_POST['caller'])) {
                Application\Message::error('Falta convocador');
                $error = true;
            } else {
                $name = $_POST['name'];
                $caller = empty($_POST['caller']) ? $_SESSION['user']->id : $_POST['caller'];

                $errors = array();

                // deberiamos poder crearla
                $call = new Model\Call;
                if ($call->create($name, $caller, $errors)) {

                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($call->id, 'call');
                    $log->populate('usuario admin/convocador crea convocatoria', 'admin/calls',
                        \vsprintf('El usuario %s ha creado una nueva convocatoria, %s', array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('call', $call->name, $call->id))
                        ));
                    $log->doAdmin('call');
                    unset($log);

                } else {
                    Application\Message::error(Text::get('call-create-fail'));
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

        private function view ($id, $show = 'index') {
            //activamos la cache para esta llamada
            \Goteo\Core\DB::cache(true);

            $call = Model\Call::get($id, Lang::current());

            if (!$call instanceof Model\Call) {
                Application\Message::error('Ha habido algun errror al cargar la convocatoria solicitada');
                throw new Redirection("/");
            } else {
                $call->logo = Model\Image::get($call->logo);
                // el fondo es el campo  backimage
                $call->image = Model\Image::get($call->backimage);

                // entradas blog
                $call->posts = Model\Call\Post::get($id);
            }

            // solamente se puede ver publicamente si
            // - es el dueño
            // - es un admin con permiso
            // - es otro usuario y el proyecto esta available: en aceptacion, en campaña, financiado
            if (($call->status > 2)
                || $call->owner == $_SESSION['user']->id // es su proyecto
                || (isset($_SESSION['admin_node']) && $_SESSION['admin_node'] == \GOTEO_NODE) // es admin de central
                || $call->isAdmin($_SESSION['user']->id) // la tiene asignada
                || isset($_SESSION['user']->roles['superadmin']) // es superadmin
            ) {

                if (!\in_array($show, array('index', 'splash', 'info', 'projects', 'terms'))) {
                    $show = 'index';
                }

                // si viene especificado el estado que se quiere previsualizar
                if (isset($_GET['preview'])) {
                    // cambiamos el estado
                    switch($_GET['preview']) {
                        case 'apply':
                            $call->status = 3;
                            break;
                        case 'campaign':
                            $call->status = 4;
                            break;
                    }


                }

                if ($show == 'projects') {

                    $call->projects = Model\Call\Project::get($call->id, array('published'=>true));

                    if ($call->status < 4 || empty($call->projects))
                        throw new Redirection("/call/".$call->id);
                }

                $call->categories = Model\Call\Category::getNames($call->id);
                $call->icons = Model\Call\Icon::getNames($call->id);

                // array de datos en redes sociales (algunos están en $call directamente)
                $social = (object) array(
                    'fbappid' => $call->fbappid, // Id de la campaña en faceboook
                    'tweet' => $call->tweet, // texto de tweet para el boton "tweet"
                    'author' => '',  // id twitter del convocador
                    'tags' => array(), // hashtags de la campaña (obtenidos del texto de tweet)
                    'buzz' => array() // lista de items de buzz en twitter
                );
                $social->author = str_replace(
                        array(
                            'https://',
                            'http://',
                            'www.',
                            'twitter.com/',
                            '#!/',
                            '@'
                        ), '', $call->user->twitter);

                // para el buzz en la portada
                if ($show == 'index') {
                    $matches = array();
                    preg_match_all('/#([a-zA-Z0-9_\-]+)/', $call->tweet, $matches);
                    if (!empty($matches)) {
                        $social->tags = $matches[0];
                    }

                    $tsQuery = '';

                    // configuración especial de buzz
                    $buzzConf = $call->getConf();

                    if (!empty($social->tags)) {
                        // si solo un hashtag
                        if (isset($buzzConf) && $buzzConf->first) {
                            $tsQuery .= $social->tags[0];
                        } else {
                            $tsQuery .= implode(', OR ', $social->tags);
                        }
                    }

                    if (!empty($social->author)) {
                        // propios
                        if (!isset($buzzConf) || $buzzConf->own)  {
                            $tsQuery .= ($tsQuery == '') ? 'from:' . $social->author : ' OR from:' . $social->author;
                        }

                        // menciones
                        if (!isset($buzzConf) || $buzzConf->mention)  {
                            $tsQuery .= ($tsQuery == '') ? '@' . $social->author : ' OR @' . $social->author;
                        }
                    }

                    $social->buzz_debug = "https://api.twitter.com/1.1/search/tweets.json?q=".  urlencode($tsQuery);
                    $social->buzz = Buzz::getTweets($tsQuery, true);
                }

                // filtro proyectos por categoria
                if ($show == 'projects' || $show == 'info') {
                    $filters = array(
                        'published' => true
                    );
                    if (isset($_GET['filter']) && is_numeric($_GET['filter'])) {
                        $filters['category'] = $_GET['filter'];
                        $filter = $_GET['filter'];
                    }
                    $call->projects = Model\Call\Project::get($call->id, $filters);
                }

                return View::get('call/'.$show.'.html.php', array ('call' => $call, 'social' => $social, 'filter' => $filter));
            } else {
                // no lo puede ver
                throw new Redirection("/");
            }
        }

        private function apply ($id) {
            $call = Model\Call::getMini($id);

            if (!$call instanceof Model\Call) {
                Application\Message::error(Text::get('call-apply-failed'));
                throw new Redirection("/");
            }

            if ($call->expired) {
                Application\Message::error(Text::get('call-apply-expired'));
                throw new Redirection("/project/create");
            } else {
                Application\Message::info(Text::get('call-apply-notice', $call->name));
                $_SESSION['oncreate_applyto'] = $id;
                throw new Redirection("/project/create");
            }

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

            $user = $call->user;

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
                $user->image = $_FILES['avatar_upload'];
            }

            // tratar si quitan la imagen
            if (!empty($_POST['avatar-' . $user->avatar->hash .  '-remove'])) {
                $user->avatar->remove($errors);
                $user->image = null;
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
            /// TODO: esto es correcto? user es un objecto, call->owner?????
            $user->save($call->errors['userProfile']);
            if ($_SESSION['user'] == $call->owner)
                Model\User::flush();

            return true;
        }

        /*
         * Paso 2 - DATOS PERSONALES
         * No hay más paso 2
         *
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
         *
         */

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
                'resources',
                'scope',
                'amount',
                'maxdrop',
                'maxproj',
                'modemaxp',
                'days'
            );

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $call->$field = $_POST[$field];
                }
            }

            // Logo e imagen de fondo
            // tratar si quitan el logo
            if (!empty($_POST['logo-' . md5($call->logo) .  '-remove'])) {
                $logo = Model\Image::get($call->logo);
                $logo->remove($errors);
                $call->logo = null;
            }

            // tratar el logo que suben
            if(!empty($_FILES['logo_upload']['name'])) {
                $call->logo = $_FILES['logo_upload'];
            }

            // tratar si quitan la imagen
            if (!empty($_POST['image-' . md5($call->image) .  '-remove'])) {
                $image = Model\Image::get($call->image);
                $image->remove($errors);
                $call->image = null;
            }

            // tratar la imagen que suben
            if(!empty($_FILES['image_upload']['name'])) {
                $call->image = $_FILES['image_upload'];
            }

            // tratar si quitan la imagen de fondo de las paginas
            if (!empty($_POST['backimage-' . md5($call->backimage) .  '-remove'])) {
                $backimage = Model\Image::get($call->backimage);
                $backimage->remove($errors);
                $call->backimage = null;
            }

            // tratar la imagen que suben
            if(!empty($_FILES['backimage_upload']['name'])) {
                $call->backimage = $_FILES['backimage_upload'];
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
         * Paso 4 - Colaboradores (banners y sponsors)
         */
        private function process_supports(&$call, &$errors) {
            if (!isset($_POST['process_supports'])) {
                return false;
            }

            // campos que guarda este paso: texto para el tweet, el Id de la app de facebook
            $fields = array(
                'tweet',
                'fbappid'
            );

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $call->$field = $_POST[$field];
                }
            }

            // tratar banners existentes
            foreach ($call->banners as $key => $banner) {

                // quitar las colaboraciones marcadas para quitar
                if (!empty($_POST["banner-{$banner->id}-remove"])) {
                    unset($call->banners[$key]);
                    continue;
                }

                if (isset($_POST['banner-' . $banner->id . '-id'])) {
                    $banner->name = $_POST['banner-' . $banner->id . '-name'];
                    $banner->url = $_POST['banner-' . $banner->id . '-url'];
                    $banner->order = $_POST['banner-' . $banner->id . '-order'];
                }

                // si quitan la imagen
                if (!empty($_POST["banner-{$banner->id}-image_remove"])) {
                    $banner->image = '';
                }

                // tratar la imagen que suben
                if(!empty($_FILES['banner-' . $banner->id . '-image_upload']['name'])) {
                    $banner->image = $_FILES['banner-' . $banner->id . '-image_upload'];
                }

            }

            // tratar sponsors existentes
            foreach ($call->sponsors as $key => $sponsor) {

                // quitar las colaboraciones marcadas para quitar
                if (!empty($_POST["sponsor-{$sponsor->id}-remove"])) {
                    unset($call->sponsors[$key]);
                    continue;
                }

                if (isset($_POST['sponsor-' . $sponsor->id . '-name'])) {
                    $sponsor->name = $_POST['sponsor-' . $sponsor->id . '-name'];
                    $sponsor->url = $_POST['sponsor-' . $sponsor->id . '-url'];
                    $sponsor->order = $_POST['sponsor-' . $sponsor->id . '-order'];
                }

                // si quitan la imagen
                if (!empty($_POST["sponsor-{$sponsor->id}-image_remove"])) {
                    $sponsor->image = '';
                }

                // tratar la imagen que suben
                if(!empty($_FILES['sponsor-' . $sponsor->id . '-image_upload']['name'])) {
                    $sponsor->image = $_FILES['sponsor-' . $sponsor->id . '-image_upload'];
                }

            }

            // añadir nuevo banner
            if (!empty($_POST['banner-add'])) {
                $call->banners[] = new Model\Call\Banner(array(
                    'call'  => $call->id,
                    'name'  => 'Nuevo banner',
                    'url'   => '',
                    'image' => '',
                    'order' => Model\Call\Banner::next($call->id)
                ));
            }

            // añadir nuevo sponsor
            if (!empty($_POST['sponsor-add'])) {
                $call->sponsors[] = new Model\Call\Sponsor(array(
                    'call'  => $call->id,
                    'name'  => 'Nuevo patrocinador',
                    'url'   => '',
                    'image' => '',
                    'order' => Model\Call\Sponsor::next($call->id)
                ));
            }

            return true;
        }

        /*
         * Paso 5 - PREVIEW
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
