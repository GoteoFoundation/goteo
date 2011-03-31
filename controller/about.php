<?php

namespace Goteo\Controller {
    
    class About extends \Goteo\Core\Controller {
        
        public function index ($title = null) {
            
            $title = 'About' . (isset($title) ? ' / ' . ucfirst($title) : '');
            
            include 'view/about/sample.html.php';
            
        }
        
    }
    
}