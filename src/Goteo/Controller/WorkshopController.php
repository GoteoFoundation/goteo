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

use Goteo\Application\Lang;
use Goteo\Application\View;
use Goteo\Core\Controller;
use Goteo\Model\Workshop;
use Goteo\Model\Workshop\WorkshopSponsor;

class WorkshopController extends Controller {

    public function __construct() {
        View::setTheme('responsive');
    }

    /**
     * Show workshop landing
     */
    public function indexAction($id) {

        $workshop= Workshop::get($id, Lang::Current());
        $event_type= $workshop->event_type ?: 'other';
        $relatedWorkshops= Workshop::getAll(['event_type' => $event_type, 'excluded' => $id ]);
        $footer_sponsors=$workshop->getSponsors(WorkshopSponsor::TYPE_FOOTER);


        if($event_type=='fundlab' ||$event_type=='fundlab-esil')
            $relatedWorkshops = array_merge(
                Workshop::getAll(['event_type' => 'fundlab-esil', 'excluded' => $id ]),
                Workshop::getAll(['event_type' => 'fundlab', 'excluded' => $id ])
            );

        return $this->viewResponse('workshop/index', [
            'workshop' => $workshop,
            'related_workshops' => $relatedWorkshops,
            'footer_sponsors' =>    $footer_sponsors
        ]);
    }
}
