<?php

namespace Goteo\Controller {

	use Goteo\Model\Content;

	class Dashboard extends \Goteo\Core\Controller {
		
		public function index () {
			
			$contents = new Content('dashboard');
            $title = $contents->title;
            $message = $contents->message;
            $modules = $contents->modules;

            include 'view/index.html.php';

		}
		
	}
	
}