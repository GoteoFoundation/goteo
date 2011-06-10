<?php

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Model\Project,
        Goteo\Core\Redirection;

    class Widget extends \Goteo\Core\Controller {
        
        public function project ($id) {

            $project  = Project::get($id);

            if (! $project instanceof  Project) {
                throw new Redirection('/', Redirection::TEMPORARY);
            }

            return new View('view/widget/project.html.php', array('project' => $project));
            
            throw new Redirection('/fail', Redirection::TEMPORARY);
        }
        
    }
    
}