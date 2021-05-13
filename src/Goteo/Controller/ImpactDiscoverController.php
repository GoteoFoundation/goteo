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

use Goteo\Application\View;
use Goteo\Application\Session;
use Goteo\Model\Category;
use Goteo\Model\Project;
use Goteo\Model\Project\ProjectLocation;

class ImpactDiscoverController extends \Goteo\Core\Controller {

    public function __construct() {
        // Cache & replica read activated in this controller
        $this->dbReplica(true);
        $this->dbCache(true);

        // \Goteo\Core\DB::replica(true);
        View::setTheme('responsive');
    }

    
    /*
     * Discover projects, general page
     */
    public function indexAction ($filter = '', Request $request) {
        

        return $this->viewResponse('impact_discover/index');

    }

}

