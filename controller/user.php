<?php

namespace Goteo\Controller {

	use Goteo\Core\Error,
		Goteo\Model;        

	class User extends \Goteo\Core\Controller {
		
		/**
		 * Usuarios
		 * <id> = Perfil usuario
		 * <vacio> = Listado de usuarios
		 * 
		 * @param string $id
		 */
		public function index ($id = null) {
			if(empty($id)) {
				$users = Model\User::getAll();
				var_dump($users); // @FIXME: Pruebas
			}
			else {
				$user = Model\User::get($id);
				var_dump($user); // @FIXME: Pruebas
				if ($user === false) {
					throw new Error(404);
				}
			}
			include 'view/user/profile.html.php';            
		}
                        
        public function register () {
            
            include 'view/user/register.html.php';
            
        }
        
        public function edit () {
            
            include 'view/user/edit.html.php';
            
        }
        
    }
    
}
