<?php

namespace Goteo\Controller {
    
    use Goteo\Core\Error,
        Goteo\Model\Project;
    
    class Project extends \Goteo\Core\Controller {
        
        public function index ($id = null) {
            
            if ($id === null) {

                
            } else {
            
                $project = Project::get($id);
            
                if ($project === false) {
                    throw new Error(404);
                }
                
                include 'view/project/overview.html.php';                
                
            }
            
            
        }
        
        public function register () {
            
            include 'view/project/register.html.php'; 
            
        }
        
        
    }
    
}
