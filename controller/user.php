<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\Redirection,
		Goteo\Model,
        Goteo\Library\Text,
        Goteo\Core\View;

	class User extends \Goteo\Core\Controller {

	    /**
	     * Página pública de perfil de usuario.
	     * @param string $id   id de usuario
	     */
		public function index ($id) {

            $user = Model\User::get($id);

            return new View (
                'view/user/profile.html.php',
                array(
                    'user' => $user
                )
            );

//		    throw new Redirection('/user/profile/' .  $id, Redirection::PERMANENT);
		}

        /**
         * Registro de usuario.
         */
        public function register () {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            	$errors = array();
                if (strcmp($_POST['email'], $_POST['remail']) !== 0) {
                    $errors['email'] = Text::get('error register email confirm');
                }
                if(strcmp($_POST['password'], $_POST['rpassword']) !== 0) {
                    $errors['password'] = Text::get('error register password confirm');
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
            // ACL::check(__CLASS__, __FUNCTION__);
            $user = $_SESSION['user'];

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $errors = array();
			    process_userData($user, $errors);
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

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $errors = array();
			    process_userProfile($user, $errors);
			}

            return new View (
                'view/user/edit.profile.html.php',
                array(
                    'user' => $user,
                    'errors' => $errors
                )
            );
        }



        //-----------------------------------------------
        // Métodos privados para el tratamiento de datos
        //-----------------------------------------------
        /*
         * Procesar los datos sensibles
         */
        private function process_userData(&$user, &$errors) {
            // el metodo save no nos sirve para nada
        }
        
        /*
         * Procesar la información pública del usuario
         */
        private function process_userProfile(&$user, &$errors) {
            // tratar la imagen y ponerla en la propiedad avatar
            // __FILES__

            $fields = array(
                'name',
                'avatar',
                'about',
                'keywords',
                'contribution',
                'twitter',
                'facebook',
                'linkedin'
            );

            foreach ($fields as $field) {
                if (isset($_POST[$field]))
                    $user->$field;
            }

            $user->saveInfo($errors);


            //intereses, si viene en el post
            if (isset($_POST['interests'])) {
                // añadir los que vienen
                foreach ($_POST['interests'] as $int) {
                    if (!in_array($int, $user->interests)) {
                        $interest = new Model\User\Interest();

                        $interest->id = $int;
                        $interest->user = $user->id;

                        $interest->save($errors);
                        $user->interests[] = $interest;
                    }
                }

                // quitar los que no vienen
                foreach ($user->interests as $key=>$int) {
                    if (!in_array($int, $_POST['interests'])) {
                        $interest = new Model\User\Interest();

                        $interest->id = $int;
                        $interest->user = $user->id;

                        if ($interest->remove($errors))
                            unset($user->interests[$key]);
                    }
                }
            }

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

            $user->check($errors); // checkea errores
        }

        //-------------------------------------------------------------
        // Hasta aquí los métodos privados para el tratamiento de datos
        //-------------------------------------------------------------

    }

}