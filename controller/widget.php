<?php

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Model\Project,
        Goteo\Core\Redirection,
		Goteo\Library\WallFriends,
        Goteo\Core\Error;

    class Widget extends \Goteo\Core\Controller {
        
        public function project ($id) {

            $project  = Project::get($id, LANG);

            if (! $project instanceof  Project) {
                throw new Redirection('/', Redirection::TEMPORARY);
            }

            return new View('view/widget/project.html.php', array('project' => $project, 'global' => true));
            
            throw new Redirection('/fail', Redirection::TEMPORARY);
        }

        public function wof ($id, $width = 608, $all_avatars = 1) {
			if($wof = new WallFriends($id,$all_avatars)) {
				echo $wof->html($width);
			}
			else {
				throw new Error(Error::NOT_FOUND);
			}
        }


    }
    
}