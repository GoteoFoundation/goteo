<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Exception\ModelException;

use Goteo\Model\Project;

class StatsApiController extends AbstractApiController {
    public function __construct() {
        parent::__construct();
        // Activate cache & replica read for this controller
        $this->dbReplica(true);
        $this->dbCache(true);
    }


    /**
     * Calculate de investors required for the minimum
     */
    public function investorsRequiredAction(Request $request) {

        if ($request->isMethod('post')) {
            $minimum = $request->request->get('minimum');
        } else {
            $minimum = $request->query->get('minimum');
        }

        //Get the investors

        $average = Project::getInvestAverage();

        $investors=ceil($minimum/$average);

        return $this->jsonResponse($investors);

    }


}
