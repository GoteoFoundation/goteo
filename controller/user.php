<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\Redirection,
		Goteo\Model;

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
                    $errors['email'] = 'La comprobación de email no coincide.';
                }
                if(strcmp($_POST['password'], $_POST['rpassword']) !== 0) {
                    $errors['password'] = 'La comprobación de contraseña no coincide.';
                }
                if(empty($errors)) {
                	$user = new Model\User();
                	$user->id = $_POST['username'];
                	$user->email = $_POST['email'];
                	$user->password = $_POST['password'];
                	$user->save($errors);
                	if(empty($errors)) {
                	  throw new Redirection('/user/profile/' .  $user->id);
                	}
                }
           	    // Devuelve los valores a la vista
           	    extract($_POST);
            }
            include 'view/user/register.html.php';
        }

        /**
         * Modificación perfil de usuario.
         */
        public function edit () {
            ACL::check(__CLASS__, __FUNCTION__);
            $user = $_SESSION['user'];
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			    // @TODO: Guardar datos
				echo '<pre>' . print_r($_POST, 1) . '</pre>';
			}
            include 'view/user/edit.html.php';
        }

        /**
         * Perfil público de usuario.
         *
         * @param string $id    Nombre de usuario
         */
        public function profile ($id) {
            $user = Model\User::get($id);
            include 'view/user/profile.html.php';
        }

    }

}