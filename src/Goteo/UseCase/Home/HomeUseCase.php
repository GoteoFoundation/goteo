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
        $footprints = Footprint::getList();

        $filters = $this->projectFilters->getFilters('promoted');
        $projects_by_footprint = [];
        $sdg_by_footprint = [];
        $footprintImpactData = [];

        foreach ($footprints as $footprint) {
            $footprintImpactData[$footprint->id] = $footprint->getAllImpactData();
            $projects_by_footprint[$footprint->id] = Project::getByFootprint([
                'footprints' => $footprint->id,
                'rand' => true,
                'amount_bigger_than' => 7000
            ]);
            $sdg_by_footprint[$footprint->id] = Sdg::getList(['footprint' => $footprint->id]);
        }

        return new HomeUseCaseResponse(
            Project::getList($filters, null, 0, HomeUseCaseResponse::LIMIT),
            Project::getList($filters, null, 0, 0, true),
            Stories::getList(['active' => true]),
            Node::getAll(['status' => 'active', 'type' => 'channel']),
            Banner::getAll(true),
            Stats::create('home_stats'),
            $footprints,
            Home::getAll(Config::get('node'), 'index'),
            $this->getSponsors(),
            $projects_by_footprint,
            $sdg_by_footprint,
            $footprintImpactData,
        );
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
