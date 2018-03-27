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

use Goteo\Model\License;

class LicensesApiController extends AbstractApiController {
    public function __construct() {
        parent::__construct();
        // Activate cache & replica read for this controller
        $this->dbReplica(true);
        $this->dbCache(true);
    }

    /**
     * Simple listing of license
     * GET filters:
     * ?icon=... filters by icon
     * ?group=... filters by group (open|regular)
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function licensesAction(Request $request) {
        $icon = $request->query->get('icon');
        $group = $request->query->get('group');

        $licenses = License::getAll($icon, $group);

        return $this->jsonResponse($licenses);
    }

}
