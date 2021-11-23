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
use Goteo\Core\Controller;
use Goteo\Model\Category;
use Goteo\Model\Footprint;
use Goteo\Model\Project;
use Goteo\Model\Node;
use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\Sdg;
use Goteo\Util\Map\MapOSM;

class ImpactDiscoverController extends Controller {

    public function __construct() {
        // Cache & replica read activated in this controller
        $this->dbReplica(true);
        $this->dbCache(true);

        View::setTheme('responsive');
    }

    private function getQueryParameters(Request $request):array {

        $filters = [];
        if ($request->query->has('channel')) {
            $filters['channel'] = strip_tags($request->query->get('channel'));
        }

        if ($request->query->has('sdgs') && !empty(strip_tags($request->query->get('sdgs'))) ){
            $filters['sdgs'] = explode(',', strip_tags($request->query->get('sdgs')));
        }

        if ($request->query->has('footprints') && !empty(strip_tags($request->query->get('footprints'))) && strip_tags($request->query->get('footprints')) != 'all'){
            $filters['footprints'] = explode(',', strip_tags($request->query->get('footprints')));
        }

        return $filters;
    }
    
    /*
     * Discover projects, general page
     */
    public function indexAction (Request $request) {
        
        $filters = $this->getQueryParameters($request);

        $sdgSelected = $filters['sdgs'] ?? [];
        $channelSelected = $filters['channel'] ?? '';
        $footprintsSelected = $filters['footprints'] ?? [];

        $footprints = Footprint::getList();
        $sdgs_count = Sdg::getList([],0,0,true);
        $sdgs = Sdg::getList([],0,$sdgs_count);
        $channels = Node::getList();

        $total = Project::getByFootprintOrSDGs($filters, 0, 0, true);
        $projects = Project::getByFootprintOrSDGs($filters, 0, 9);

        return $this->viewResponse('impact_discover/index', [
            'footprints' => $footprints,
            'sdgs' => $sdgs,
            'sdgSelected' => $sdgSelected,
            'footprintsSelected' => $footprintsSelected,
            'channelSelected' => $channelSelected,
            'projects' => $projects,
            'total' => $total,
            'channels' => $channels,
            'view' => 'list_projects'
        ]);

    }

    public function mapAction(Request $request) {

        $filters = $this->getQueryParameters($request);

        $sdgSelected = $filters['sdgs'] ?? [];
        $channelSelected = $filters['channel'] ?? '';
        $footprintsSelected = $filters['footprints'] ?? [];

        $map = new MapOSM('100%');
        $footprints = Footprint::getList();
        $sdgs_count = Sdg::getList([],0,0,true);
        $sdgs = Sdg::getList([],0,$sdgs_count);
        $channels = Node::getList();

        $total = Project::getByFootprintOrSDGs($filters, 0, 0, true);
        $projects = Project::getByFootprintOrSDGs($filters, 0, 9);

        return $this->viewResponse('impact_discover/map', [
            'footprints' => $footprints,
            'sdgs' => $sdgs,
            'sdgSelected' => $sdgSelected,
            'footprintsSelected' => $footprintsSelected,
            'channelSelected' => $channelSelected,
            'map' => $map,
            'projects' => $projects,
            'total' => $total,
            'channels' => $channels,
            'view' => 'map'
        ]);
    }

    public function mosaicAction(Request $request) {
        $filters = $this->getQueryParameters($request);

        $sdgSelected = $filters['sdgs'] ?? [];
        $channelSelected = $filters['channel'] ?? '';
        $footprintsSelected = $filters['footprints'] ?? '';

        $footprints = Footprint::getList();
        $sdgs_count = Sdg::getList([],0,0,true);
        $sdgs = Sdg::getList([],0,$sdgs_count);
        $channels = Node::getList();

        $total = Project::getByFootprintOrSDGs($filters, 0, 0, true);
        $projects = Project::getByFootprintOrSDGs($filters, 0, 9);

        return $this->viewResponse('impact_discover/mosaic', [
            'footprints' => $footprints,
            'sdgs' => $sdgs,
            'sdgSelected' => $sdgSelected,
            'footprintsSelected' => $footprintsSelected,
            'channelSelected' => $channelSelected,
            'projects' => $projects,
            'total' => $total,
            'channels' => $channels,
            'view' => 'mosaic'
        ]);
    }

}

