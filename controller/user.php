<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Model,
        Goteo\Library\Text,
        Goteo\Core\View;

	class User extends \Goteo\Core\Controller {

	    /**
	     * Atajo al perfil de usuario.
	     * @param string $id   Nombre de usuario
	     */
		public function index ($id) {
		    throw new Redirection('/user/profile/' .  $id, Redirection::PERMANENT);
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
                	  throw new Redirection('/user/profile/' .  $user->id);
                	}
                }
            }
            return new View (
                'view/user/register.html.php',
                $_POST
            );
        }

        /**
         * Modificación perfil de usuario.
         */
        public function edit () {
            if(!ACL::check(__CLASS__, __FUNCTION__)) {
                throw new Error(403);
            }

            $user = $_SESSION['user'];

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $errors = array();

                $user->name = $_POST['name'];
                $user->avatar = $_POST['avatar'];
                $user->about = $_POST['about'];
                $user->keywords = $_POST['keywords'];
                $user->contribution = $_POST['contribution'];
                $user->twitter = $_POST['twitter'];
                $user->facebook = $_POST['facebook'];
                $user->linkedin = $_POST['linkedin'];
                $user->interests = $_POST['interests'];
                $user->save($errors);

                //tratar webs existentes
                foreach ($user->webs as $key=>$web) {
                    // primero mirar si lo estan quitando
                    if (isset($_POST['remove-web' . $web->id]) && $_POST['remove-web' . $web->id] == 1) {
                        if ($web->remove($errors))
                            unset($user->webs[$key]);
                        continue; // no tratar esta
                    }

                    if (isset($_POST['web' . $web->id])) {
                        $web->user = $user->id;
                        $web->url = $_POST['web' . $web->id];

                        $web->save($errors);
                    }
                }
                //tratar nueva web
                if (isset($_POST['nweb']) && !empty($_POST['nweb'])) {

                    $web = new Model\User\Web();

                    $web->id = '';
                    $web->user = $user->id;
                    $web->url = $_POST['nweb'];

                    $web->save($errors);

                    $user->webs[] = $web;
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

    }

}