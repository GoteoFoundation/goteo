<?php

namespace Goteo\Controller {

	use Goteo\Model\Content;

	class Dashboard extends \Goteo\Core\Controller {
		
		public function index () {
			
			$content = new Content('dashboard');
            $title = $content->title;
            $message = $content->message;
            $modules = $content->modules;

            include 'view/index.html.php';

		}
		
	}
	
}