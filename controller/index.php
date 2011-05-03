<?php

namespace Goteo\Controller {

    use Goteo\Core\View;

    class Index extends \Goteo\Core\Controller {
        
        public function index ($node = null) {
            
            return new View('view/index.html.php');
            
        }
        
    }
    
}