<?php

namespace Goteo\Controller {

	use Goteo\Model\Content;

    class Index extends \Goteo\Core\Controller {
        
        public function index ($node = null) {

			$contents = new Content('home', $node);
            $title = $contents->title;
            $message = $contents->message;
            $modules = $contents->modules; // los modulos de la home ordenados segun la prioridad gestionada, instancias de model/content/module.php
            
            include 'view/index.html.php';
            
        }
        
    }
    
}