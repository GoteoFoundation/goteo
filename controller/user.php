<?php

namespace Goteo\Controller {

	use Goteo\Core\Redirection,
		Goteo\Model;

	class User extends \Goteo\Core\Controller {

	    /**
	     * Atajo al perfil de usuario.
	     * @param string $id   Nombre de usuario
	     */
		public function index ($id) {
		    throw new Redirection('/user/profile/' .  $id);
		}
		
		/**
		 * Inicio de sesión
		 * Si no se le pasan parámetros carga el tpl de identificación.
		 * 
		 * @param string $username Nombre de usuario
		 * @param string $password Contraseña
		 */
        public function login () {
        	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        	    $username = $_POST['username'];
        	    $password = $_POST['password'];
        		if (false !== ($user = (Model\User::login($username, $password)))) {
        			$_SESSION['user'] = $user;
        			throw new Redirection('/dashboard');
        		}
        		else {
        		    $error = true;
        		}
        	}
        	include 'view/user/login.html.php';
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
            Model\User::restrict();             
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