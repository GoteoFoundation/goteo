<?php

namespace Goteo\Controller {

	use Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Core\View,
		Goteo\Model,
        Goteo\Library\Feed,
        Goteo\Library\Text,
        Goteo\Library\Message,
        Goteo\library\Listing;

	class User extends \Goteo\Core\Controller {

	    /**
	     * Atajo al perfil de usuario.
	     * @param string $id   Nombre de usuario
	     */
		public function index ($id, $show = '') {
		    throw new Redirection('/user/profile/' .  $id . '/' . $show, Redirection::PERMANENT);
		}

        /**
         * Inicio de sesión.
         * Si no se le pasan parámetros carga el tpl de identificación.
         *
         * @param string $username Nombre de usuario
         * @param string $password Contraseña
         */
        public function login () {

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['login'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                if (false !== ($user = (\Goteo\Model\User::login($username, $password)))) {
                    $_SESSION['user'] = $user;
                    if (!empty($_POST['return'])) {
                        throw new Redirection($_POST['return']);
                    } else {
                        throw new Redirection('/dashboard');
                    }
                }
                else {
                    Message::Error(Text::get('login-fail'));
                }
            }

            return new View ('view/user/login.html.php');

        }

        /**
         * Cerrar sesión.
         */
        public function logout() {
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time()-42000, '/');
            }
            session_destroy();
            throw new Redirection('/');
            die;
        }
        /**
         * Registro de usuario.
         */
        public function register () {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            	$errors = array();
                if (strcmp($_POST['email'], $_POST['remail']) !== 0) {
                    $errors['remail'] = Text::get('error-register-email-confirm');
                }
                if(strcmp($_POST['password'], $_POST['rpassword']) !== 0) {
                    $errors['rpassword'] = Text::get('error-register-password-confirm');
                }
                
                $user = new Model\User();
                $user->userid = $_POST['userid'];
                $user->name = $_POST['username'];
                $user->email = $_POST['email'];
                $user->password = $_POST['password'];
                $user->save($errors);

                if(empty($errors)) {
                  Message::Info(Text::get('user-register-success'));
                  throw new Redirection('/user/login');
                } else {
                    foreach ($errors as $field=>$text) {
                        Message::Error($text);
                    }
                }
            }
            return new View (
                'view/user/login.html.php',
                array(
                    'errors' => $errors
                )
            );
        }

        /**
         * Modificación perfil de usuario.
         */
        public function edit () {
            $user = $_SESSION['user'];

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			    $errors = array();
                // E-mail
                if($_POST['change_email']) {
                    if(empty($_POST['user_nemail'])) {
                        $errors['email'] = Text::get('error-user-email-empty');
                    }
                    elseif(!\Goteo\Library\Check::mail($_POST['user_nemail'])) {
                        $errors['email'] = Text::get('error-user-email-invalid');
                    }
                    elseif(empty($_POST['user_remail'])) {
                        $errors['email']['retry'] = Text::get('error-user-email-empty');
                    }
                    elseif (strcmp($_POST['user_nemail'], $_POST['user_remail']) !== 0) {
                        $errors['email']['retry'] = Text::get('error-user-email-confirm');
                    }
                    else {
                        $user->email = $_POST['user_nemail'];
                    }
                }
                // Contraseña
                if($_POST['change_password']) {
                    if(empty($_POST['user_password'])) {
                        $errors['password'] = Text::get('error-user-password-empty');
                    }
                    elseif(!Model\User::login($user->id, $_POST['user_password'])) {
                        $errors['password'] = Text::get('error-user-wrong-password');
                    }
                    elseif(empty($_POST['user_npassword'])) {
                        $errors['password']['new'] = Text::get('error-user-password-empty');
                    }
                    elseif(!\Goteo\Library\Check::password($_POST['user_npassword'])) {
                        $errors['password']['new'] = Text::get('error-user-password-invalid');
                    }
                    elseif(empty($_POST['user_rpassword'])) {
                        $errors['password']['retry'] = Text::get('error-user-password-empty');
                    }
                    elseif(strcmp($_POST['user_npassword'], $_POST['user_rpassword']) !== 0) {
                        $errors['password']['retry'] = Text::get('error-user-password-confirm');
                    }
                    else {
                        $user->password = $_POST['user_npassword'];
                    }
                }
                // Avatar
                if(!empty($_FILES['user_avatar']['name'])) {
                    $user->avatar = $_FILES['user_avatar'];
                }

                // tratar si quitan la imagen
                if (!empty($_POST['avatar-' . $user->avatar->id .  '-remove'])) {
                    $user->avatar->remove('user');
                    $user->avatar = '';
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
                if(!empty($_POST['user_webs']['remove'])) {
                    $user->webs = array('remove' => $_POST['user_webs']['remove']);
                }
                elseif(!empty($_POST['user_webs']['add']) && !empty($_POST['user_webs']['add'][0]) ) {
                    $user->webs = array('add' => $_POST['user_webs']['add']);
                }
                else {
                    $user->webs = array('edit', $_POST['user_webs']['edit']);
                }
                if($user->save($errors)) {
                    // Refresca la sesión.
                    $user = Model\User::flush();
                    if (isset($_POST['save'])) {
                        throw new Redirection('/dashboard');
                    } else {
                        throw new Redirection('/user/edit');
                    }
                }
			}

            return new View (
                'view/user/edit.html.php',
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
        public function profile ($id, $show = 'profile', $category = null) {

            if (!in_array($show, array('profile', 'investors', 'sharemates', 'message'))) {
                $show = 'profile';
            }

            $user = Model\User::get($id);

            if (!$user instanceof Model\User || $user->hide) {
                throw new Redirection('/', Redirection::PERMANENT);
            }
                
            
            $viewData = array();
            $viewData['user'] = $user;

            $projects = Model\Project::ofmine($id, true);

            //mis cofinanciadores
            // array de usuarios con:
            //  foto, nombre, nivel, cantidad a mis proyectos, fecha ultimo aporte, nº proyectos que cofinancia
            $investors = array();
            foreach ($projects as $kay=>$project) {

                /*
                 * PASAMOS DE ESTA RESTRICCION POR AHORA
                // quitamos los no publicados o caducados
                if ($project->status < 3 || $project->status > 5) {
                    unset ($projects[$kay]);
                    continue;
                }
                 *
                 */

                foreach (Model\Invest::investors($project->id) as $key=>$investor) {
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

            $viewData['investors'] = $investors;

            // comparten intereses
            $viewData['shares'] = Model\User\Interest::share($id, $category);

            if (!empty($category)) {
                $viewData['category'] = $category;
            }

            // proyectos que cofinancio
            $invested = Model\User::invested($id, true);

            // agrupacion de proyectos que cofinancia y proyectos suyos
            $viewData['lists'] = array();
            if (!empty($invested)) {
                $viewData['lists']['invest_on'] = Listing::get($invested, 2);
            }
            if (!empty($projects)) {
                $viewData['lists']['my_projects'] = Listing::get($projects, 2);
            }

            return new View ('view/user/'.$show.'.html.php', $viewData);
        }

        /**
         * Activación usuario.
         *
         * @param type string	$token
         */
        public function activate($token) {
            $query = Model\User::query('SELECT id FROM user WHERE token = ?', array($token));
            if($id = $query->fetchColumn()) {
                $user = Model\User::get($id);
                if(!$user->active) {
                    $user->active = true;
                    if($user->save($errors)) {
                        Message::Info(Text::get('user-activate-success'));
                        $_SESSION['user'] = $user;

                        /*
                         * Evento Feed
                         */
                        $log = new Feed();
                        $log->title = 'nuevo usuario registrado (confirmado)';
                        $log->url = '/admin/users';
                        $log->type = 'user';
                        $log_text = 'Nuevo usuario en Goteo %s';
                        $log_items = array(
                            Feed::item('user', $user->name, $user->id)
                        );
                        $log->html = \vsprintf($log_text, $log_items);
                        $log->add($errors);

                        // evento público
                        $log->title = $user->name;
                        $log->url = null;
                        $log->scope = 'public';
                        $log->type = 'community';
                        $log->add($errors);

                        unset($log);

                    }
                    else {
                        Message::Error($errors);
                    }
                }
                else {
                    Message::Info(Text::get('user-activate-already-active'));
                }
            }
            else {
                Message::Error(Text::get('user-activate-fail'));
            }
            throw new Redirection('/dashboard');
        }

        /**
         * Cambiar dirección de correo.
         *
         * @param type string	$token
         */
        public function changeemail($token) {
            $token = base64_decode($token);
            if(count(explode('¬', $token)) > 1) {
                $query = Model\User::query('SELECT id FROM user WHERE token = ?', array($token));
                if($id = $query->fetchColumn()) {
                    $user = Model\User::get($id);
                    $user->email = $token;
                    $errors = array();
                    if($user->save($errors)) {
                        Message::Info(Text::get('user-changeemail-success'));

                        // Refresca la sesión.
                        Model\User::flush();
                    }
                    else {
                        Message::Error($errors);
                    }
                }
                else {
                    Message::Error(Text::get('user-changeemail-fail'));
                }
            }
            else {
                Message::Error(Text::get('user-changeemail-fail'));
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
        public function recover ($token = null) {

            // si el token mola, logueo este usuario y lo llevo a su dashboard
            if (!empty($token)) {
                $token = base64_decode($token);
                $parts = explode('¬', $token);
                if(count($parts) > 1) {
                    $query = Model\User::query('SELECT id FROM user WHERE email = ? AND token = ?', array($parts[1], $token));
                    if($id = $query->fetchColumn()) {
                        if(!empty($id)) {
                            // el token coincide con el email y he obtenido una id
                            $user = Model\User::get($id);
                            $_SESSION['user'] = $user;
                            throw new Redirection('/dashboard/profile/access/recover#password');
                        }
                    }
                }

                $error = 'El código de recuperación no es válido';//Text::get('recover-token-incorrect');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['recover'])) {
                $username = $_POST['username'];
                $email    = $_POST['email'];
                if (Model\User::recover($username, $email)) {
                    // se pue recuperar
                    $message = 'Te hemos enviado un email para recuperar tu cuenta. Verifica también la carpeta de correo no deseado o spam.';
                    //Text::get('recover-email-sended');
                }
                else {
                    $error = 'No se puede recuperar ninguna cuenta con estos datos';
                    //Text::get('recover-request-fail');
                }
            }

            return new View (
                'view/user/recover.html.php',
                array(
                    'error'   => $error,
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
        public function leave ($token = null) {

            // si el token mola, lo doy de baja
            if (!empty($token)) {
                $token = base64_decode($token);
                $parts = explode('¬', $token);
                if(count($parts) > 1) {
                    $query = Model\User::query('SELECT id FROM user WHERE email = ? AND token = ?', array($parts[1], $token));
                    if($id = $query->fetchColumn()) {
                        if(!empty($id)) {
                            // el token coincide con el email y he obtenido una id
                            if (Model\User::cancel($id)) {
                                Message::Info('Te hemos dado de baja'); //Text::get
                                throw new Redirection('/user/login');
                            } else {
                                Message::Error('No hemos podido darte de baja, contáctanos'); //Text::get
                                throw new Redirection('/user/login');
                            }
                        }
                    }
                }

                $error = 'El código no es válido';//Text::get('leave-token-incorrect');
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['leaving'])) {
                $email    = $_POST['email'];
                if (Model\User::leaving($email)) {
                    // se pue recuperar
                    $message = 'Te hemos enviado un email para completar el proceso. Verifica también la carpeta de correo no deseado o spam.';
                    //Text::get('leave-email-sended');
                }
                else {
                    $error = 'No hemos encontrado ninguna cuenta con este email en nuestra base de datos';
                    //Text::get('leave-request-fail');
                }
            }

            return new View (
                'view/user/leave.html.php',
                array(
                    'error'   => $error,
                    'message' => $message
                )
            );

        }

    }

}