<?php

namespace Goteo\Controller {

	use Goteo\Model;

	class Prueba extends \Goteo\Core\Controller {
		
		/**
		 * Usuarios
		 * <id> = Perfil usuario
		 * <vacio> = Listado de usuarios
		 *
		 * @param string $id
		 */
		public function index ($user = null) {

			// Pruebas modelo usuario
			if(empty($id)) {
				$list = Model\User::getAll();
				echo '<pre>' . print_r($list, 1) . '</pre>';
			}
			else {
				$data = Model\User::get($user);
				echo '<pre>' . print_r($data, 1) . '</pre>';
/*				if ($data === false) {
					throw new Error(404);
				}*/
			}


			$content = new Model\Content('test');
			echo '<pre>' . print_r($content, 1) . '</pre>';
			echo '<hr>';
			echo Model\Text::get('test uno');
			echo '<hr>';
			echo Model\Text::get('test dos');

			/*
            $title = $content->title;
            $message = $content->message;
            $modules = $content->modules;
			$widgets = $content->widgets;
			$stuff = $content->stuff;

            include 'view/index.html.php';
			 *
			 */

		}
		
	}
	
}