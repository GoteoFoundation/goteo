<?php
namespace Goteo\Controller {

	use \Goteo\Library\Paypal,
		\Goteo\Model\User;

	class Prueba extends \Goteo\Core\Controller {

		public function index () {

			/*
			 *  Prueba de paypal
			 *  Usaremos adaptive payments
			 */
//			echo Text::get('test uno');



			/*
			 * Prueba de contenidos
			$content = new Model\Content('test');
			echo '<pre>' . print_r($content, 1) . '</pre>';
            $title = $content->title;
            $message = $content->message;
            $modules = $content->modules;
			$widgets = $content->widgets;
			$stuff = $content->stuff;

            include 'view/index.html.php';
			 *
			 */

		}
		

		/**
		 * Usuarios
		 * <id> = Perfil usuario
		 * <vacio> = Listado de usuarios
		 *
		 * @param string $id
		 */
		public function users ($user = null) {

			// Pruebas modelo usuario
			if(empty($id)) {
				$list = User::getAll();
				echo '<pre>' . print_r($list, 1) . '</pre>';
			}
			else {
				$data = User::get($user);
				echo '<pre>' . print_r($data, 1) . '</pre>';
/*				if ($data === false) {
					throw new Error(404);
				}*/
			}
		}
		
	}
	
}