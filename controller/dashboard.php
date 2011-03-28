<?php

namespace Goteo\Controller {

	use Goteo\Library\Content,
		Goteo\Model;

	class Dashboard extends \Goteo\Core\Controller {

		/*
		 *  La manera de obtener el id del usuario validado cambiará al tener la session
		 */
		public function index () {
			Model\User::restrict();	

            $message = "Hola $id<br />";

			$projects = Model\Project::ofmine($id);

            include 'view/dashboard.html.php';

		}
		
	}
	
}