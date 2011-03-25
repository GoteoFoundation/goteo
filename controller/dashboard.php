<?php

namespace Goteo\Controller {

	use Goteo\Library\Content,
		Goteo\Model\Project;

	class Dashboard extends \Goteo\Core\Controller {

		/*
		 *  La manera de obtener el id del usuario validado cambiará al tener la session
		 */
		public function index () {

			// si tenemos usuario logueado
			$id = $_SESSION['user'];

			if (!$id) {
				header('Location: /');
				die;
			}

            $message = "Hola $id<br />";

			if ($id == 'root') {
				$message .= '<a href="/texts">Gestión de textos</a>';
			}

			$projects = Project::ofmine($id);

            include 'view/dashboard.html.php';

		}
		
	}
	
}