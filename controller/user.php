<?php

namespace Goteo\Controller {

	use Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Core\View,
		Goteo\Model,
        Goteo\Library\Text,
        Goteo\Library\Message;

	class User extends \Goteo\Core\Controller {

	    /**
	     * Atajo al perfil de usuario.
	     * @param string $id   Nombre de usuario
	     */
		public function index ($id) {
		    throw new Redirection('/user/profile/' .  $id, Redirection::PERMANENT);
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
                    throw new Redirection('/dashboard');
                }
                else {
                    $error = true;
                }
            }

            return new View (
                'view/user/login.html.php',
                array(
                    'login_error' => !empty($error)
                )
            );

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
                    $errors['email'] = Text::get('error-register-email-confirm');
                }
                if(strcmp($_POST['password'], $_POST['rpassword']) !== 0) {
                    $errors['password'] = Text::get('error-register-password-confirm');
                }
                if(empty($errors)) {
                	$user = new Model\User();
                	$user->name = $_POST['username'];
                	$user->email = $_POST['email'];
                	$user->password = $_POST['password'];
                	$user->save($errors);
                	if(empty($errors)) {
                	  Message::Info(Text::get('user-register-success'));
                	  throw new Redirection('/user/login');
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
        public function profile ($id) {
            $user = Model\User::get($id);
            return new View (
                'view/user/profile.html.php',
                array(
                    'user' => $user
                )
            );
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

    }

}