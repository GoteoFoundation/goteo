<?php

namespace Goteo\Controller {

	use Goteo\Model\Content;

    class Index extends \Goteo\Core\Controller {
        
        public function index ($node = null) {

			$content = new Content('home', $node);
            $title = $content->title;
            $message = $content->message;
            $modules = $content->modules; // los modulos de la home ordenados segun la prioridad gestionada, instancias de model/content/module.php
            
            include 'view/index.html.php';
            
        }
        
    }
    
}