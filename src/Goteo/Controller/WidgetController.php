<?php
/*
* This file is part of the Goteo Package.
*
* (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
*
* For the full copyright and license information, please view the README.md
* and LICENSE files that was distributed with this source code.
*/

namespace Goteo\Controller;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\View;
use Goteo\Model\Project;
use Goteo\Library\WallFriends;
use Symfony\Component\HttpFoundation\Response;

class WidgetController extends \Goteo\Core\Controller {

    public function __construct() {
        // Cache & replica read activated in this controller
        \Goteo\Core\DB::cache(true);
        \Goteo\Core\DB::replica(true);
        View::setTheme('responsive');
    }

    public function projectAction ($id) {

        $project  = Project::get($id);

        if (! $project instanceof  Project) {
            throw new ModelNotFoundException();
        }

        return $this->viewResponse('widget/project', ['project' => $project]);

    }

    public function wofAction ($id, $width = 608, $all_avatars = 1) {
		if($wof = new WallFriends(Project::get($id), $all_avatars)) {
			return new Response($wof->html($width, true));
		}
		else {
			throw new ModelNotFoundException();
		}
    }


}
