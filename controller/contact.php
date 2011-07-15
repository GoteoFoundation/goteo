<?php

namespace Goteo\Controller {

    use Goteo\Core\Redirection;

    class Contact extends \Goteo\Core\Controller {
        
        public function index () {

            throw new Redirection('/about/contact');
            
        }
        
    }
    
}