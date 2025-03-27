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

use Goteo\Application\View;
use Goteo\Application\Config;
use Goteo\Model\Banner;
use Goteo\Model\Footprint;
use Goteo\Model\Home;
use Goteo\Model\Project;
use Goteo\Model\Sdg;
use Goteo\Model\Stories;
use Goteo\Model\Node;
use Goteo\Model\Sponsor;
use Goteo\Repository\AnnouncementRepository;
use Goteo\Util\Stats\Stats;
use Symfony\Component\HttpFoundation\Response;


class IndexController extends DiscoverController
{

    public function __construct()
    {
        $this->dbReplica(true);
        $this->dbCache(true);
        View::setTheme('responsive');
    }

    public function indexAction(): Response
    {
        $limit = 24;
        $filters = $this->getProjectFilters('promoted');
        $projects = Project::getList($filters, null, 0, $limit);
        $total_projects = Project::getList($filters, null, 0, 0, true);
        $stories = Stories::getList(['active' => true]);
        $channels = Node::getAll(['status' => 'active', 'type' => 'channel']);
        $banners = Banner::getAll(true);
        $stats = Stats::create('home_stats');
        $sponsors = $this->getSponsors();
        $footprints = Footprint::getList();
        $home = Home::getAll(Config::get('node'), 'index');

        $projects_by_footprint = [];
        $sdg_by_footprint = [];
        foreach ($footprints as $footprint) {
            $footprintImpactData[$footprint->id] = $footprint->getListOfImpactData(['source' => 'manual']);
            $projects_by_footprint[$footprint->id] = Project::getByFootprint(['footprints' => $footprint->id, 'rand' => true, 'amount_bigger_than' => 7000]);
            $sdg_by_footprint[$footprint->id] = Sdg::getList(['footprint' => $footprint->id]);
        }

        $announcementRepository = new AnnouncementRepository();
        $announcementList = $announcementRepository->getActiveList();


        return $this->viewResponse('home/index', [
            'banners'   => $banners,
            'projects'  => $projects,
            'total_projects'  => $total_projects,
            'limit'     => $limit,
            'limit_add' => 12, // Limit for javascript on addSlick
            'stories'   => $stories,
            'channels'  => $channels,
            'stats'     => $stats,
            'sponsors'  => $sponsors,
            'footprints' => $footprints,
            'home' => $home,
            'projects_by_footprint' => $projects_by_footprint,
            'sdg_by_footprint' => $sdg_by_footprint,
            'footprint_impact_data' => $footprintImpactData,
            'announcements' => $announcementList
        ]);
    }

    private function getSponsors(): array
    {
        $sponsors = [];
        foreach (Sponsor::getTypes() as $type) {
            $sponsors[$type] = Sponsor::getList(['type' => $type]);
        }
        return $sponsors;
    }
}
