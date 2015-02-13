<?php

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Library\WallFriends,
        Goteo\Core\Error;

    class Widget extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador index
            \Goteo\Core\DB::cache(true);
        }

        public function project ($id) {

            $project  = Model\Project::getMedium($id, LANG);

            if (! $project instanceof  Model\Project) {
                throw new Redirection('/', Redirection::TEMPORARY);
            }

            return new View('widget/project.html.php', array('project' => $project, 'global' => true));

            throw new Redirection('/fail', Redirection::TEMPORARY);
        }

        public function wof ($id, $width = 608, $all_avatars = 1) {
			if($wof = new WallFriends(Model\Project::get($id), $all_avatars)) {
				echo $wof->html($width, true);
			}
			else {
				throw new Error(Error::NOT_FOUND);
			}
        }


    }

}
