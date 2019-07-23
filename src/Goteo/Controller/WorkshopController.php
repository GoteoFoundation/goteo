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

use Symfony\Component\HttpFoundation\Request;

use Goteo\Library\Text;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\Lang;
use Goteo\Application\View;
use Goteo\Application\EventListener\ProjectCallListener;
use Goteo\Library\Buzz;
use Goteo\Model\Project;
use Goteo\Model\Image;
use Goteo\Model\Call;
use Goteo\Model\Matcher;
use Goteo\Model\Workshop;
use Goteo\Model\Sphere;
use Goteo\Model\Stories;
use Goteo\Model\Page;
use Goteo\Model\SocialCommitment;

class WorkshopController extends \Goteo\Core\Controller {

    public function __construct() {
        View::setTheme('responsive');
    }

    /**
     * Show workshop landing
     * @param  [type] $id   Channel id
     */
    public function indexAction($id, Request $request) {

        $workshop= Workshop::get($id);
        $type= $workshop->type ? $workshop->type : 'other';
        $related_workshops= Workshop::getAll(['type' => $type, 'excluded' => $id ]);

        if($type=='fundlab' ||$type=='fundlab-esil')
            $related_workshops=array_merge(Workshop::getAll(['type' => 'fundlab-esil', 'excluded' => $id ]), Workshop::getAll(['type' => 'fundlab', 'excluded' => $id ]));



        return $this->viewResponse(
            'workshop/index',
            [
                'workshop'              => $workshop,
                'related_workshops'     => $related_workshops

            ]
        );
    }
}

