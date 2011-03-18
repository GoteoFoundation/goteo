<?php

namespace Goteo\Controller {

	use Goteo\Model\Content,
		Goteo\Model\Text;

	class Prueba extends \Goteo\Core\Controller {
		
		public function index () {

			$contents = new Content('test');
			echo '<pre>' . print_r($contents, 1) . '</pre>';
			echo '<hr>';
			echo Text::get('test uno');
			echo '<hr>';
			echo Text::get('test dos');

			/*
            $title = $contents->title;
            $message = $contents->message;
            $modules = $contents->modules;
			$widgets = $contents->widgets;
			$stuff = $contents->stuff;

            include 'view/index.html.php';
			 *
			 */

		}
		
	}
	
}