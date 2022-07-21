<?php

namespace Goteo\UseCase\Home;

use Goteo\Application\Config;
use Goteo\Model\Banner;
use Goteo\Model\Footprint;
use Goteo\Model\Home;
use Goteo\Model\Node;
use Goteo\Model\Project;
use Goteo\Model\Sdg;
use Goteo\Model\Sponsor;
use Goteo\Model\Stories;
use Goteo\Util\Stats\Stats;

class HomeUseCase
{
    private Project\ProjectFilters $projectFilters;

    public function __construct()
    {
        $this->projectFilters = new Project\ProjectFilters();
    }

    public function execute(): HomeUseCaseResponse
    {
        $response = new HomeUseCaseResponse();
        $footprints = Footprint::getList();

        $filters = $this->projectFilters->getFilters('promoted');
        $projects_by_footprint = [];
        $sdg_by_footprint = [];

        foreach ($footprints as $footprint) {
            $footprintImpactData[$footprint->id] = $footprint->getAllImpactData();
            $projects_by_footprint[$footprint->id] = Project::getByFootprint([
                'footprints' => $footprint->id,
                'rand' => true,
                'amount_bigger_than' => 7000
            ]);
            $sdg_by_footprint[$footprint->id] = Sdg::getList(['footprint' => $footprint->id]);
        }

        $response
            ->setProjects(Project::getList($filters, null, 0, $response->getLimit()))
            ->setTotalProjects(Project::getList($filters, null, 0, 0, true))
            ->setStories(Stories::getList(['active' => true]))
            ->setChannels(Node::getAll(['status' => 'active', 'type' => 'channel']))
            ->setBanners(Banner::getAll(true))
            ->setStats(Stats::create('home_stats'))
            ->setFootprints($footprints)
            ->setHomeItems(Home::getAll(Config::get('node'), 'index'))
            ->setSponsors($this->getSponsors())
            ->setFootprintImpactData($footprintImpactData)
            ->setProjectsByFootprint($projects_by_footprint)
            ->setSdgByFootprint($sdg_by_footprint)
        ;

        return $response;
    }

    /**
     * @return Sponsor[]
     */
    private function getSponsors(): array
    {
        $sponsors = [];

        foreach(Sponsor::getTypes() as $type) {
            $sponsors[$type] = Sponsor::getList(['type' => $type]);
        }

        return $sponsors;
    }
}
