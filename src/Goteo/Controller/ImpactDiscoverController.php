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

use Goteo\Repository\DataSetRepository;
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
use Symfony\Component\HttpFoundation\Response;

class ImpactDiscoverController extends Controller {

    public function __construct() {
        // Cache & replica read activated in this controller
        $this->dbReplica(true);
        $this->dbCache(true);

        View::setTheme('responsive');
    }

    private function setImpactDiscoverContext() {
        $footprints = Footprint::getList();
        $sdgs_count = Sdg::getList([],0,0,true);
        $sdgs = Sdg::getList([],0,$sdgs_count);
        $channels = Node::getAll(['status' => 'active']);

        $this->contextVars([
            'footprints' => $footprints,
            'sdgs' => $sdgs,
            'channels' => $channels,
        ]);
    }

    private function getQueryParameters(Request $request):array {

        $filters = [];
        if ($request->query->has('channel')) {
            $filters['channel'] = strip_tags($request->query->getAlnum('channel'));
        }

        if ($request->query->has('sdgs') && !empty(strip_tags($request->query->getDigits('sdgs'))) ){
            $filters['sdgs'] = explode(',', strip_tags($request->query->getDigits('sdgs')));
        }

        if ($request->query->has('footprints') && !empty(strip_tags($request->query->getDigits('footprints'))) && strip_tags($request->query->getDigits('footprints')) != 'all'){
            $filters['footprints'] = explode(',', strip_tags($request->query->getDigits('footprints')));
        }

        return $filters;
    }

    /*
     * Discover projects, general page
     */
    public function indexAction (Request $request) {

        $this->setImpactDiscoverContext();
        $filters = $this->getQueryParameters($request);

        $sdgSelected = $filters['sdgs'] ?? [];
        $channelSelected = $filters['channel'] ?? '';
        $footprintsSelected = $filters['footprints'] ?? [];

        $filters['order'] = 'amount';
        $filters['minpercentage'] = 0.2; // minimum amount of 20%

        $total = Project::getByFootprintOrSDGs($filters, 0, 0, true);
        $projects = Project::getByFootprintOrSDGs($filters, 0, 9);

        return $this->viewResponse('impact_discover/index', [
            'sdgSelected' => $sdgSelected,
            'footprintsSelected' => $footprintsSelected,
            'channelSelected' => $channelSelected,
            'projects' => $projects,
            'total' => $total,
            'view' => 'list_projects'
        ]);

    }

    public function mapAction(Request $request) {

        $this->setImpactDiscoverContext();
        $filters = $this->getQueryParameters($request);

        $sdgSelected = $filters['sdgs'] ?? [];
        $channelSelected = $filters['channel'] ?? '';
        $footprintsSelected = $filters['footprints'] ?? [];

        $map = new MapOSM('100%');
        $total = Project::getByFootprintOrSDGs($filters, 0, 0, true);
        $projects = Project::getByFootprintOrSDGs($filters, 0, 9);

        return $this->viewResponse('impact_discover/map', [
            'sdgSelected' => $sdgSelected,
            'footprintsSelected' => $footprintsSelected,
            'channelSelected' => $channelSelected,
            'map' => $map,
            'projects' => $projects,
            'total' => $total,
            'view' => 'map'
        ]);
    }

    public function mosaicAction(Request $request) {
        $this->setImpactDiscoverContext();
        $filters = $this->getQueryParameters($request);

        $sdgSelected = $filters['sdgs'] ?? [];
        $channelSelected = $filters['channel'] ?? '';
        $footprintsSelected = $filters['footprints'] ?? '';

        $total = Project::getByFootprintOrSDGs($filters, 0, 0, true);
        $projects = Project::getByFootprintOrSDGs($filters, 0, 9);

        return $this->viewResponse('impact_discover/mosaic', [
            'sdgSelected' => $sdgSelected,
            'footprintsSelected' => $footprintsSelected,
            'channelSelected' => $channelSelected,
            'projects' => $projects,
            'total' => $total,
            'view' => 'mosaic'
        ]);
    }

    public function dataSetsAction(Request $request): Response {
        $this->setImpactDiscoverContext();
        $filters = $this->getQueryParameters($request);

        $sdgSelected = $filters['sdgs'] ?? [];
        $channelSelected = $filters['channel'] ?? '';
        $footprintsSelected = $filters['footprints'] ?? [];

        $dataSetsRepository = new DataSetRepository();
        $dataSets = $dataSetsRepository->getListByFootprintAndSDGs($filters);

        return $this->viewResponse('impact_discover/data_sets', [
            'sdgSelected' => $sdgSelected,
            'footprintsSelected' => $footprintsSelected,
            'channelSelected' => $channelSelected,
            'dataSets' => $dataSets,
            'view' => 'data_sets'
        ]);
    }
}

