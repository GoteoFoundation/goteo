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
use Goteo\Model\Footprint;
use Goteo\Model\Project;
use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\Sdg;
use Goteo\Util\Map\MapOSM;

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
    public function indexAction (Request $request) {
        
        $filters = [];
        $sdgSelected = [];

        if ($request->query->has('sdgs') && !empty($request->query->get('sdgs'))) {
            $sdgSelected = explode(',', $request->query->get('sdgs'));
            $filters['sdgs'] = $sdgSelected;
        }

        $footprints = Footprint::getList();
        $sdgs_count = Sdg::getList([],0,0,true);
        $sdgs = Sdg::getList([],0,$sdgs_count);

        $total = Project::getByFootprintOrSDGs($filters, 0, 0, true);
        $projects = Project::getByFootprintOrSDGs($filters, 0, 9);

        return $this->viewResponse('impact_discover/index', [
            'footprints' => $footprints,
            'sdgs' => $sdgs,
            'sdgSelected' => $sdgSelected,
            'projects' => $projects,
            'total' => $total,
            'view' => 'list_projects'
        ]);

    }

    public function mapAction(Request $request) {

        $sdgSelected = [];
        if ($request->query->has('sdgs') && !empty($request->query->get('sdgs'))) {
            $sdgSelected = explode(',', $request->query->get('sdgs'));
            $filters['sdgs'] = $sdgSelected;
        }

        $map = new MapOSM('100%');
        $footprints = Footprint::getList();
        $sdgs_count = Sdg::getList([],0,0,true);
        $sdgs = Sdg::getList([],0,$sdgs_count);

        $total = Project::getByFootprintOrSDGs([], 0, 0, true);
        $projects = Project::getByFootprintOrSDGs([], 0, 9);

        return $this->viewResponse('impact_discover/map', [
            'footprints' => $footprints,
            'sdgs' => $sdgs,
            'sdgSelected' => $sdgSelected,
            'map' => $map,
            'projects' => $projects,
            'total' => $total,
            'view' => 'map'
        ]);
    }

    public function mosaicAction(Request $request) {
        $filters = [];
        $sdgSelected = [];

        if ($request->query->has('sdgs') && !empty($request->query->get('sdgs'))) {
            $sdgSelected = explode(',', $request->query->get('sdgs'));
            $filters['sdgs'] = $sdgSelected;
        }

        $footprints = Footprint::getList();
        $sdgs_count = Sdg::getList([],0,0,true);
        $sdgs = Sdg::getList([],0,$sdgs_count);

        $total = Project::getByFootprintOrSDGs($filters, 0, 0, true);
        $projects = Project::getByFootprintOrSDGs($filters, 0, 9);

        return $this->viewResponse('impact_discover/mosaic', [
            'footprints' => $footprints,
            'sdgs' => $sdgs,
            'sdgSelected' => $sdgSelected,
            'projects' => $projects,
            'total' => $total,
            'view' => 'mosaic'
        ]);
    }

}

