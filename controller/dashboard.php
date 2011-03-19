<?php

namespace Goteo\Controller {

	use Goteo\Model\Content;

	class Dashboard extends \Goteo\Core\Controller {

		/*
		 *  La manera de obtener el id del usuario validado cambiará al tener la session
		 */
		public function index ($id = null) {

			// si tenemos id

			$content = new Content('dashboard');
            $title = $content->title;
            $message = $content->message . $id;
            $modules = $content->modules;

            include 'view/index.html.php';

		}
		
	}
	
}