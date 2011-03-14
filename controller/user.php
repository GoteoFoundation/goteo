<?php

namespace Goteo\Controller {
    
    use Goteo\Core\Error,
        Goteo\Model\User;        
    
    class User extends \Goteo\Core\Controller {
        
        public function index ($id) {
            
            $user = User::get($id);
            
            if ($user === false) {
                throw new Error(404);
            }
            
            include 'view/user/profile.html.php';            
            
        }
                        
        public function register () {
            
            include 'view/user/register.html.php';
            
        }
        
        public function edit () {
            
            include 'view/user/edit.html.php';
            
        }
        
    }
    
}
