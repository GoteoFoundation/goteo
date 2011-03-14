<?php

namespace Goteo\Controller {
    
    class Index extends \Goteo\Core\Controller {
        
        public function index ($hey = null) {
            
            $message = 'Hello world';
            
            include 'view/index.html.php';
            
        }
        
    }
    
}