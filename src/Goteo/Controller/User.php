<?php

namespace Goteo\Controller {

    use Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Core\View,
        Goteo\Model,
        Goteo\Application\Session,
        Goteo\Application\Cookie,
        Goteo\Library,
        Goteo\Library\Feed,
        Goteo\Library\Text,
        Goteo\Library\OAuth\SocialAuth,
        Goteo\Library\Listing;

    class User extends \Goteo\Core\Controller {

        /**
         * Atajo al perfil de usuario.
         * @param string $id   Nombre de usuario
         */
        public function index($id, $show = '') {
            throw new Redirection('/user/profile/' . $id . '/' . $show, Redirection::PERMANENT);
        }

        public function raw($id) {
            $user = Model\User::get($id, LANG);
            \trace($user);
            die;
        }

        /**
         * Inicio de sesión.
         * Si no se le pasan parámetros carga el tpl de identificación.
         *
         * @param string $username Nombre de usuario
         * @param string $password Contraseña
         */
        public function login($username = '') {

            // si está logueado, redirigir a dashboard
            if (Session::isLogged()) {
                throw new Redirection('/dashboard/activity');
            }

/*
            // esto debería verificar que esté instalado el certificado SSL
            if (GOTEO_ENV != 'local' && $_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['HTTPS'] !== 'on') {
                $ret = (!empty($_REQUEST['return'])) ? '?return='.$_REQUEST['return'] : '';
                throw new Redirection(SEC_URL.'/user/login'.$ret);
            }
*/
            // si venimos de la página de aportar
            if (isset($_POST['amount'])) {
                $_SESSION['invest-amount'] = $_POST['amount'];
                $msg = Text::get('user-login-required-login');
                $msg .= (!empty($_POST['amount'])) ? '. ' . Text::get('invest-alert-investing') . ' ' . $_POST['amount'] . $_SESSION['currency'] : '';
                Library\Message::Info($msg);
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['login'])) {
                $username = \strtolower($_POST['username']);
                $password = $_POST['password'];
                if (false !== ($user = (Model\User::login($username, $password)))) {
                    self::loginUser($user);
                } else {
                    Library\Message::Error(Text::get('login-fail'));
                }
            } elseif (empty($_SESSION['user']) && !empty($_COOKIE['goteo_user'])) {
                // si tenemos cookie de usuario
                return new View('user/login.html.php', array('username'=>$_COOKIE['goteo_user']));
            }

            return new View('user/login.html.php');
        }

        /**
         * Logea un usuario en session
         * @param  Model\User $user     El objecto User a logoear
         * @param  boolean   $redirect Si ha que procesar las directivas de redireccion o no
         * @return Model\User           El usuario si no hay redirección
         */
        static public function loginUser(Model\User $user, $redirect = true) {
            Session::setUser($user);

            // creamos una cookie
            Cookie::store('goteo_user', $user->id);

            if (!empty($user->lang)) {
                $_SESSION['lang'] = $user->lang;
            }
            unset($_SESSION['admin_menu']);

            if (isset($user->roles['admin'])) {
                // posible admin de nodo
                if ($node = Model\Node::getAdminNode($user->id)) {
                    $_SESSION['admin_node'] = $node;
                }
            } else {
                unset($_SESSION['admin_node']);
            }

            if($redirect) {
                if (!empty($_REQUEST['return'])) {
                    throw new Redirection($_REQUEST['return']);
                } elseif (!empty($_SESSION['jumpto'])) {
                    $jumpto = $_SESSION['jumpto'];
                    unset($_SESSION['jumpto']);
                    throw new Redirection($jumpto);
                } elseif (isset($user->roles['admin']) || isset($user->roles['superadmin'])) {
                    throw new Redirection('/admin');
                } else {
                    throw new Redirection('/dashboard');
                }
            }

            return $user;
        }

        /**
         * Cerrar sesión.
         */
        public function logout() {
            $lang = '?lang=' . Session::get('lang');
            Session::destroy();
            throw new Redirection('/' . $lang);
            die;
        }

        /**
         * Registro de usuario.
         */
        public function register() {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                foreach ($_POST as $key => $value) {
                    $_POST[$key] = trim($value);
                }

                $errors = array();

                if (strcmp($_POST['email'], $_POST['remail']) !== 0) {
                    $errors['remail'] = Text::get('error-register-email-confirm');
                }
                if (strcmp($_POST['password'], $_POST['rpassword']) !== 0) {
                    $errors['rpassword'] = Text::get('error-register-password-confirm');
                }

                $user = new Model\User();
                $user->userid = $_POST['userid'];
                $user->name = $_POST['username'];
                $user->email = $_POST['email'];
                $user->password = $_POST['password'];
                $user->active = true;
                $user->node = \NODE_ID;

                $user->save($errors);

                if (empty($errors)) {
                    Library\Message::Info(Text::get('user-register-success'));

                    self::loginUser(Model\User::get($user->id));

                } else {
                    foreach ($errors as $field => $text) {
                        Library\Message::Error($text);
                    }
                }
            }
            return new View(
                            'user/login.html.php',
                            array(
                                'errors' => $errors
                            )
            );
        }

        /**
         * Registro de usuario desde oauth
         */
        public function oauth_register() {

            //comprovar si venimos de un registro via oauth
            if ($_POST['provider']) {

                $provider = $_POST['provider'];

                $oauth = new SocialAuth($provider);
                //importar els tokens obtinguts anteriorment via POST
                if ($_POST['tokens'][$oauth->provider]['token'])
                    $oauth->tokens[$oauth->provider]['token'] = $_POST['tokens'][$oauth->provider]['token'];
                if ($_POST['tokens'][$oauth->provider]['secret'])
                    $oauth->tokens[$oauth->provider]['secret'] = $_POST['tokens'][$oauth->provider]['secret'];

                // print_r($_POST['tokens']);print_r($oauth->tokens[$oauth->provider]);die;

                $user = new Model\User();
                $user->userid = $_POST['userid'];
                $user->email = $_POST['email'];
                $user->active = true;

                //resta de dades
                foreach ($oauth->user_data as $k => $v) {
                    if ($_POST[$k]) {
                        $oauth->user_data[$k] = $_POST[$k];
                        if (in_array($k, $oauth->import_user_data))
                            $user->$k = $_POST[$k];
                    }
                }
                //si no existe nombre, nos lo inventamos a partir del userid
                if (trim($user->name) == '') {
                    $user->name = ucfirst($user->userid);
                }

                //print_R($user);print_r($oauth);die;
                //no hará falta comprovar la contraseña ni el estado del usuario
                $skip_validations = array('password', 'active');

                //si el email proviene del proveedor de oauth, podemos confiar en el y lo confirmamos por defecto
                if ($_POST['provider_email'] == $user->email) {
                    $user->confirmed = 1;
                }

                $query = Model\User::query('SELECT id,password FROM user WHERE email = ?', array($user->email));
                if ($u = $query->fetchObject()) {
                    if ($u->password == sha1($_POST['password'])) {
                        //ok, login en goteo e importar datos
                        //y fuerza que pueda logear en caso de que no tenga contraseña o email sin confirmar
                        if ($user = $oauth->goteoLogin(true)) {
                            //login!
                            self::loginUser($user);
                        }
                        else {
                            //si no: registrar errores
                            Library\Message::Error($oauth->last_error);
                            throw new Redirection(SEC_URL . '/user/login');
                        }
                    } else {
                        // si tiene contraseña permitir vincular la cuenta,
                        // si no mensaje de error
                        if($u->password) {
                            if($_POST) {
                                Library\Message::Error(Text::get('login-fail'));
                            }
                            return new View(
                                            'user/confirm_account.html.php',
                                            array(
                                                'oauth' => $oauth,
                                                'user' => Model\User::get($u->id)
                                            )
                            );
                        }
                        else {
                            Library\Message::Error(Text::get('oauth-goteo-user-password-error'));
                        }
                    }
                } elseif ($user->save($errors, $skip_validations)) {
                    //si el usuario se ha creado correctamente, login en goteo e importacion de datos
                    //y fuerza que pueda logear en caso de que no tenga contraseña o email sin confirmar
                    if ($user = $oauth->goteoLogin(true)) {
                        //login!
                        self::loginUser($user);
                    }
                    else {
                        //si no: registrar errores
                        Library\Message::Error($oauth->last_error);
                    }
                } elseif ($errors) {
                    foreach ($errors as $err => $val) {
                        if ($err != 'email' && $err != 'userid')
                            Library\Message::Error($val);
                    }
                }
            }
            return new View(
                            'user/confirm.html.php',
                            array(
                                'errors' => $errors,
                                'oauth' => $oauth
                            )
            );
        }

        /**
         * Registro de usuario a traves de Oauth (libreria HybridOauth, openid, facebook, twitter, etc).
         */
        public function oauth() {

            $errors = array();
            if (isset($_GET["provider"]) && $_GET["provider"]) {
                $oauth = new SocialAuth($_GET["provider"]);

                if ($oauth->authenticate()) {
                    if ($user = $oauth->goteoLogin()) {
                        //login!
                        self::loginUser($user);
                    }
                    else {
                        //si falla: error o formulario de confirmación
                        if ($oauth->error_type == 'user-not-exists') {
                            return new View(
                                            'user/confirm.html.php',
                                            array(
                                                'oauth' => $oauth
                                            )
                            );
                        }
                        // existe usuario, formulario de vinculacion
                        elseif ($oauth->error_type == 'user-password-exists') {
                            Library\Message::Error($oauth->last_error);
                            return new View(
                                            'user/confirm_account.html.php',
                                            array(
                                                'oauth' => $oauth,
                                                'user' => Model\User::get($oauth->user_data['username'])
                                            )
                            );
                        }
                        else {
                            Message::Error($oauth->last_error);
                            throw new Redirection(SEC_URL . '/user/login');
                        }
                    }
                }
                else {
                    //si falla: error, si no siempre se redirige al proveedor
                    Library\Message::Error($oauth->last_error);
                    throw new Redirection(SEC_URL . '/user/login');
                }
            }

            return new View(
                            'user/login.html.php',
                            array(
                                'errors' => $errors
                            )
            );
        }

        /**
         * Registro instantáneo de usuario mediante email
         * (Si existe devuelve id pero no loguea)
         */
        static public function instantReg($email, $name = '') {

            $errors = array();

            // Si el email es de un usuario existente, asignar el aporte a ese usuario (no lo logueamos)
            $query = Model\User::query("
                    SELECT
                        id,
                        name,
                        email
                    FROM user
                    WHERE BINARY email = :email
                    ",
                array(
                    ':email'    => trim($email)
                )
            );
            if($row = $query->fetchObject()) {
                if (!empty($row->id)) {
                    Library\Message::Error(Text::get('error-user-email-exists'));
                    return false;
                }
            }


            // si no existe, creamos un registro de usuario enviando el mail (y lo dejamos logueado)
            // ponemos un random en user y contraseña (el modelo mandará el mail)
            if (empty($name)) $name = substr($email, 0, strpos($email, '@'));
            $userid = substr($email, 0, strpos($email, '@')) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9);

            $user = new Model\User();
            $user->userid = $userid;
            $user->name = $name;
            $user->email = $email;
            $user->password = $name;
            $user->active = false;
            $user->node = \NODE_ID;

            if ($user->save($errors)) {
                self::loginUser(Model\User::get($user->id), false);
                Library\Message::Info(Text::get('user-register-success'));
                return $user->id;
            }

            if (!empty($errors)) {
                Library\Message::Error(implode('<br />', $errors));
            }

            return false;
        }

        /**
         * Modificación perfil de usuario.
         * Metodo Obsoleto porque esto lo hacen en el dashboard
         */
        public function edit() {
            $user = $_SESSION['user'];

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $errors = array();
                // E-mail
                if ($_POST['change_email']) {
                    if (empty($_POST['user_nemail'])) {
                        $errors['email'] = Text::get('error-user-email-empty');
                    } elseif (!\Goteo\Library\Check::mail($_POST['user_nemail'])) {
                        $errors['email'] = Text::get('error-user-email-invalid');
                    } elseif (empty($_POST['user_remail'])) {
                        $errors['email']['retry'] = Text::get('error-user-email-empty');
                    } elseif (strcmp($_POST['user_nemail'], $_POST['user_remail']) !== 0) {
                        $errors['email']['retry'] = Text::get('error-user-email-confirm');
                    } else {
                        $user->email = $_POST['user_nemail'];
                    }
                }
                // Contraseña
                if ($_POST['change_password']) {
                    /*
                     * Quitamos esta verificacion porque los usuarios que acceden mediante servicio no tienen contraseña
                     *
                      if(empty($_POST['user_password'])) {
                      $errors['password'] = Text::get('error-user-password-empty');
                      }
                      else
                     */
                    if (!Model\User::login($user->id, $_POST['user_password'])) {
                        $errors['password'] = Text::get('error-user-wrong-password');
                    } elseif (empty($_POST['user_npassword'])) {
                        $errors['password']['new'] = Text::get('error-user-password-empty');
                    } elseif (!\Goteo\Library\Check::password($_POST['user_npassword'])) {
                        $errors['password']['new'] = Text::get('error-user-password-invalid');
                    } elseif (empty($_POST['user_rpassword'])) {
                        $errors['password']['retry'] = Text::get('error-user-password-empty');
                    } elseif (strcmp($_POST['user_npassword'], $_POST['user_rpassword']) !== 0) {
                        $errors['password']['retry'] = Text::get('error-user-password-confirm');
                    } else {
                        $user->password = $_POST['user_npassword'];
                    }
                }
                // Avatar
                if (!empty($_FILES['user_avatar']['name'])) {
                    $user->avatar = $_FILES['user_avatar'];
                }

                // tratar si quitan la imagen
                if (!empty($_POST['avatar-' . $user->avatar->hash . '-remove'])) {
                    $user->avatar->remove($errors);
                    $user->avatar = null;
                }

                // Perfil público
                $user->name = $_POST['user_name'];
                $user->about = $_POST['user_about'];
                $user->keywords = $_POST['user_keywords'];
                $user->contribution = $_POST['user_contribution'];
                $user->twitter = $_POST['user_twitter'];
                $user->facebook = $_POST['user_facebook'];
                $user->linkedin = $_POST['user_linkedin'];
                // Intereses
                $user->interests = $_POST['user_interests'];
                // Páginas Web
                if (!empty($_POST['user_webs']['remove'])) {
                    $user->webs = array('remove' => $_POST['user_webs']['remove']);
                } elseif (!empty($_POST['user_webs']['add']) && !empty($_POST['user_webs']['add'][0])) {
                    $user->webs = array('add' => $_POST['user_webs']['add']);
                } else {
                    $user->webs = array('edit', $_POST['user_webs']['edit']);
                }
                if ($user->save($errors)) {
                    // Refresca la sesión.
                    $user = Model\User::flush();
                    if (isset($_POST['save'])) {
                        throw new Redirection('/dashboard');
                    } else {
                        throw new Redirection('/user/edit');
                    }
                }
            }

            return new View(
                            'user/edit.html.php',
                            array(
                                'user' => $user,
                                'errors' => $errors
                            )
            );
        }

        /**
         * Perfil público de usuario.
         *
         * @param string $id    Nombre de usuario
         */
        public function profile($id, $show = 'profile', $category = null) {

            if (!in_array($show, array('profile', 'investors', 'sharemates', 'message'))) {
                $show = 'profile';
            }

            // para debuguear, añadir ?debug=b1da4861a4edf5615ec39f07963ea8db
            $dbg = (isset($_GET['debug']) && $_GET['debug'] == md5('dbg'));

            if ($dbg)
                $ti = microtime(true);

            $user = Model\User::get($id, LANG);

            if ($dbg) {
                $tf = microtime(true);
                $tp = $tf - $ti;
                $tt = $tp;
                echo 'Tiempo de User::get = ' . $tp . ' segundos<br />';
            }

            if (!$user instanceof Model\User || $user->hide) {
                throw new Error('404', Text::html('fatal-error-user'));
            }

            //--- para usuarios públicos---
            if (empty($_SESSION['user'])) {
                // la subpágina de mensaje también está restringida
                if ($show == 'message') {
                    $_SESSION['jumpto'] = '/user/profile/' . $id . '/message';
                    Library\Message::Info(Text::get('user-login-required-to_message'));
                    throw new Redirection(SEC_URL."/user/login");
                }


                // a menos que este perfil sea de un vip, no pueden verlo
                if (!isset($user->roles['vip'])) {
                    $_SESSION['jumpto'] = '/user/profile/' . $id . '/' . $show;
                    Library\Message::Info(Text::get('user-login-required-to_see'));
                    throw new Redirection(SEC_URL."/user/login");
                }
            }

            // en la página de mensaje o en el perfil para pintar o no el botón
            if ($show == 'message' || $show == 'profile') {

                // ver si el usuario logueado (A)
                $uLoged = $_SESSION['user']->id;

                // puede enviar mensaje (mensajear)
                $user->messageable = false;  // por defecto no

                // al usuario del perfil (B)
                $uProfile = $id;

                // solamente pueden comunicarse si:

                // para debug
                if ($dbg) {
                    // un u.logueado impulsor solo puede mandar mensaje a otro impulsor
                    // si el U.logueado es impulsor (autor de proyecto publicado)
                    Model\User::isOwner($uLoged, true, $dbg);
                    echo '<br /><br />';

                    // y el U.destinatario es impulsor (autor de proyecto publicado)
                    Model\User::isOwner($uProfile, true, $dbg);
                    echo '<br /><br />';


                    // si A es cofinanciador en algún proyecto que impulsa B
                    Model\User::isInvestor($uLoged, $uProfile, $dbg);
                    echo '<br /><br />';

                    // si B es cofinanciador en algún proyecto que impulsa A
                    Model\User::isInvestor($uProfile, $uLoged, $dbg);
                    echo '<br /><br />';

                    // si A ha escrito mensaje en algún proyecto que impulsa B
                    Model\User::isParticipant($uProfile, $uLoged, $dbg);
                    echo '<br /><br />';

                    // si B ha escrito mensaje en algún proyecto que impulsa A
                    Model\User::isParticipant($uLoged, $uProfile, $dbg);
                    echo '<br /><br />';
                }

                // sin debug no da echoes
                // no hace mucha falta optimizar ya que los metodos-operadores están en cortocircuito
                // http://php.net/manual/es/language.operators.logical.php
                if (
                    ( Model\User::isOwner($uLoged, true) && Model\User::isOwner($uProfile, true) )
                    || Model\User::isInvestor($uLoged, $uProfile)
                    || Model\User::isInvestor($uProfile, $uLoged)
                    || Model\User::isParticipant($uProfile, $uLoged)
                    || Model\User::isParticipant($uLoged, $uProfile)
                )
                    $user->messageable = true;

            }

            // si ya esta en la página de mensaje
            if ($show == 'message' && !$user->messageable) {
                Library\Message::Info(Text::get('user-message-restricted'));
                throw new Redirection('/user/profile/' . $id);
            } else {
                // para el controller/message::personal
                $_SESSION['message_autorized'] = true;
            }

            /*
             *  Si es un usuario vip y tiene proyectos recomendados activados
             *   mostramos la página especial de patronos
             */
            if (isset($user->roles['vip'])) {
                $recos = Model\Patron::getList($user->id);
                if (count($recos) > 0) {
                    $vip = Model\User\Vip::get($user->id);
                    if (!empty($vip->image)) {
                        $user->avatar = $vip->image;
                    }

                    // pasarle el autodetector de urls por el about
                    $user->about = nl2br(Text::urlink($user->about));

                    // proyectos que cofinancia este vip (que no sean los que recomienda)
                    $invest_on = Model\User::invested($user->id, true);

                    $recomend = array();
                    // los proyectos que recomienda
                    foreach ($recos as $recproj) {
                        $recomend[] = $recproj->project;
                    }
                    // y quitarlos de los que cofinancia
                    foreach ($invest_on as $key => $invproj) {
                        if (in_array($invproj->id, $recomend)) {
                            unset($invest_on[$key]);
                        }
                    }

                    // agrupados para carrusel
                    $invested = Listing::get($invest_on);

                    return new View('user/patron.html.php', array('user' => $user, 'recos' => $recos, 'lists' => array('invest_on' => $invested)));
                }
            }


            $viewData = array();
            $viewData['user'] = $user;

            /* para sacar cofinanciadores */
            if ($dbg)
                $ti = microtime(true);

            $projects = Model\Project::ofmine($id, true);
            $viewData['projects'] = $projects;

            //mis cofinanciadores
            $investors = Model\Invest::myInvestors($id, 5);
            $viewData['investors'] = $investors;

/*
 *   esto lo hacemos en myInvestors
 *
 *
            // array de usuarios con:
            //  foto, nombre, nivel, cantidad a mis proyectos, fecha ultimo aporte, nº proyectos que cofinancia
            $investors = array();
            foreach ($projects as $kay => $project) {

                // quitamos los caducados
                if ($project->status == 0) {
                    unset($projects[$kay]);
                    continue;
                }

                foreach (Model\Invest::investors($project->id) as $key => $investor) {
                    // convocadores no, gracias
                    if (!empty($investor->campaign))
                        continue;

                    if (\array_key_exists($investor->user, $investors)) {
                        // ya está en el array, quiere decir que cofinancia este otro proyecto
                        // , añadir uno, sumar su aporte, actualizar la fecha
                        ++$investors[$investor->user]->projects;
                        $investors[$investor->user]->amount += $investor->amount;
                        $investors[$investor->user]->date = $investor->date;
                    } else {
                        $investors[$investor->user] = (object) array(
                                    'user' => $investor->user,
                                    'name' => $investor->name,
                                    'projects' => 1,
                                    'avatar' => $investor->avatar,
                                    'worth' => $investor->worth,
                                    'amount' => $investor->amount,
                                    'date' => $investor->date
                        );
                    }
                }
            }
*/


            if ($dbg) {
                $tf = microtime(true);
                $tp = $tf - $ti;
                $tt += $tp;
                echo 'Tiempo de sacar mis cofinanciadores = ' . $tp . ' segundos<br />';
            }

            /* para sacar sharemates */
            if ($dbg)
                $ti = microtime(true);

            // comparten intereses
            if ($show == 'profile'){
                $viewData['shares'] = Model\User\Interest::share($id, null, 6);
            }

            if ($show == 'sharemates') {

                $viewData['shares'] = Model\User\Interest::share($id, $category, 20);
                if ($show == 'sharemates' && empty($viewData['shares'])) {
                    $show = 'profile';
                }

            }

            if ($dbg) {
                $tf = microtime(true);
                $tp = $tf - $ti;
                $tt += $tp;
                echo 'Tiempo de sacar mis sharemates = ' . $tp . ' segundos. ' . $tf . ' - ' . $ti . '<br />';
            }


            if (!empty($category)) {
                $viewData['category'] = $category;
            }

            /* para sacar proyectos que cofinancio */
            if ($dbg)
                $ti = microtime(true);

            // proyectos que cofinancio
            $invested = Model\User::invested($id, true);

            if ($dbg) {
                $tf = microtime(true);
                $tp = $tf - $ti;
                $tt += $tp;
                echo 'Tiempo de sacar proyectos que cofinancio = ' . $tp . ' segundos<br />';
            }


            // agrupacion de proyectos que cofinancia y proyectos suyos
            $viewData['lists'] = array();
            if (!empty($invested)) {
                $viewData['lists']['invest_on'] = Listing::get($invested, 2);
            }
            if (!empty($projects)) {
                $viewData['lists']['my_projects'] = Listing::get($projects, 2);
            }

            if ($dbg)
                die('Tiempo total antes de saltar a la vista ' . $tt . ' segundos');


            return new View('user/' . $show . '.html.php', $viewData);
        }

        /**
         * Activación usuario.
         *
         * @param type string	$token
         */
        public function activate($token) {
            $errors = array();
            $query = Model\User::query('SELECT id FROM user WHERE token = ?', array($token));
            if ($id = $query->fetchColumn()) {
                $user = Model\User::get($id);
                if (!$user->confirmed) {
                    $user->confirmed = true;
                    $user->active = true;
                    if ($user->save($errors)) {
                        Library\Message::Info(Text::get('user-activate-success'));
                        self::loginUser($user, false);

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($user->id, 'user');
                        $log->populate('nuevo usuario registrado (confirmado)', '/admin/users', Text::html('feed-new_user', Feed::item('user', $user->name, $user->id)));
                        $log->doAdmin('user');

                        // evento público
                        $log->title = $user->name;
                        $log->url = null;
                        $log->doPublic('community');

                        unset($log);
                    } else {
                        Library\Message::Error($errors);
                    }
                } else {
                    Library\Message::Info(Text::get('user-activate-already-active'));
                }
            } else {
                Library\Message::Error(Text::get('user-activate-fail'));
            }
            throw new Redirection('/dashboard');
        }

        /**
         * Cambiar dirección de correo.
         *
         * @param type string	$token
         */
        public function changeemail($token) {
            $token = \mybase64_decode($token);
            if (count(explode('¬', $token)) > 1) {
                $query = Model\User::query('SELECT id FROM user WHERE token = ?', array($token));
                if ($id = $query->fetchColumn()) {
                    $user = Model\User::get($id);
                    $user->email = $token;
                    $errors = array();
                    if ($user->save($errors)) {
                        Library\Message::Info(Text::get('user-changeemail-success'));

                        // Refresca la sesión.
                        Model\User::flush();
                    } else {
                        Library\Message::Error($errors);
                    }
                } else {
                    Library\Message::Error(Text::get('user-changeemail-fail'));
                }
            } else {
                Library\Message::Error(Text::get('user-changeemail-fail'));
            }
            throw new Redirection('/dashboard');
        }

        /**
         * Recuperacion de contraseña
         * - Si no llega nada, mostrar formulario para que pongan su username y el email correspondiente
         * - Si llega post es una peticion, comprobar que el username y el email que han puesto son válidos
         *      si no lo son, dejarlos en el formulario y mensaje de error
         *      si son válidos, enviar email con la url y mensaje de ok
         *
         * - Si llega un hash, verificar y darle acceso hasta su dashboard /profile/access para que la cambien
         *
         * @param string $token     Codigo
         */
        public function recover($token = null) {

            // si el token mola, logueo este usuario y lo llevo a su dashboard
            if (!empty($token)) {
                $token = \mybase64_decode($token);
                $parts = explode('¬', $token);
                if (count($parts) > 1) {
                    $query = Model\User::query('SELECT id FROM user WHERE email = ? AND token = ?', array($parts[1], $token));
                    if ($id = $query->fetchColumn()) {
                        if (!empty($id)) {
                            // el token coincide con el email y he obtenido una id
                            // Activamos y dejamos de esconder el usuario
                            Model\User::query('UPDATE user SET active = 1, hide = 0, confirmed = 1 WHERE id = ?', array($id));
                            $user = Model\User::get($id);
                            self::loginUser($user, false);
                            $_SESSION['recovering'] = $user->id;
                            throw new Redirection(SEC_URL.'/dashboard/profile/access/recover#password');
                        }
                    }
                }

                $error = Text::get('recover-token-incorrect');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['recover'])) {
                $email = $_POST['email'];
                if (!empty($email) && Model\User::recover($email)) {
                    $message = Text::get('recover-email-sended');
                    unset($_POST['email']);
                    unset($_REQUEST['email']);
                } else {
                    $error = Text::get('recover-request-fail');
                }
            }

            return new View(
                            'user/recover.html.php',
                            array(
                                'error' => $error,
                                'email' => $_REQUEST['email'],
                                'message' => $message
                            )
            );
        }

        /**
         * Darse de baja
         * - Si no llega nada, mostrar formulario para que pongan el email de su cuenta
         * - Si llega post es una peticion, comprobar que el email que han puesto es válido
         *      si no es, dejarlos en el formulario y mensaje de error
         *      si es válido, enviar email con la url y mensaje de ok
         *
         * - Si llega un hash, verificar y dar de baja la cuenta (desactivar y ocultar)
         *
         * @param string $token     Codigo
         */
        public function leave($token = null) {

            // si el token mola, lo doy de baja
            if (!empty($token)) {
                $token = \mybase64_decode($token);
                $parts = explode('¬', $token);
                if (count($parts) > 1) {
                    $query = Model\User::query('SELECT id FROM user WHERE email = ? AND token = ?', array($parts[1], $token));
                    if ($id = $query->fetchColumn()) {
                        if (!empty($id)) {
                            // el token coincide con el email y he obtenido una id
                            if (Model\User::cancel($id)) {
                                Library\Message::Info(Text::get('leave-process-completed'));
                                throw new Redirection(SEC_URL.'/user/login');
                            } else {
                                Library\Message::Error(Text::get('leave-process-fail'));
                                throw new Redirection(SEC_URL.'/user/login');
                            }
                        }
                    }
                }

                $error = Text::get('leave-token-incorrect');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['leaving'])) {
                if (Model\User::leaving($_POST['email'], $_POST['reason'])) {
                    $message = Text::get('leave-email-sended');
                    unset($_POST['email']);
                    unset($_POST['reason']);
                } else {
                    $error = Text::get('leave-request-fail');
                }
            }

            return new View(
                            'user/leave.html.php',
                            array(
                                'error' => $error,
                                'message' => $message
                            )
            );
        }

        /*
         * Método para bloquear el envío de newsletter
         *
         * token es un
         *
         */
        public function unsuscribe($token = null) {

            $errors = array();
            // si el token mola, lo doy de baja
            if (!empty($token)) {
                $token = \mybase64_decode($token);
                $parts = explode('¬', $token);
                if (count($parts) > 1) {
                    $query = Model\User::query('SELECT id FROM user WHERE email = ?', array($parts[1]));
                    if ($id = $query->fetchColumn()) {
                        if (!empty($id)) {
                            // el token coincide con el email y he obtenido una id
                            Model\User::setPreferences($id, array('mailing'=>1), $errors);

                            if (empty($errors)) {
                                $message = Text::get('unsuscribe-request-success');
                            } else {
                                $error = implode('<br />', $errors);
                            }
                        }
                    } else {
                        $error = Text::get('leave-token-incorrect');
                    }
                } else {
                    $error = Text::get('leave-token-incorrect');
                }
            } else {
                $error = Text::get('leave-request-fail');
            }

            return new View(
                'user/unsuscribe.html.php',
                array(
                    'error' => $error,
                    'message' => $message
                )
            );
        }


    }

}
