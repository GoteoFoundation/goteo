<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Model,
        Goteo\Core\Redirection,
        Goteo\Application\Lang,
        Goteo\Library\WallFriends,
        Goteo\Core\Error;

    class Widget extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador index
            \Goteo\Core\DB::cache(true);
        }

        public function project ($id) {

            $project  = Model\Project::getMedium($id, Lang::current());

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
