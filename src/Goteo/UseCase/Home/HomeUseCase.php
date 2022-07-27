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
    private array $projects_by_footprint = [];
    private array $sdg_by_footprint = [];
    private array $footprintImpactData = [];

    public function __construct()
    {
        $this->projectFilters = new Project\ProjectFilters();
    }

    public function execute(): HomeUseCaseResponse
    {
        $footprints = Footprint::getList();
        $this->generateFootprintRelatedData($footprints);
        $filters = $this->projectFilters->getFilters('promoted');

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
            $this->projects_by_footprint,
            $this->sdg_by_footprint,
            $this->footprintImpactData,
        );
    }

    /**
     * @param Footprint[] $footprints
     * @return void
     */
    private function generateFootprintRelatedData(array $footprints): void
    {
        foreach ($footprints as $footprint) {
            $this->footprintImpactData[$footprint->id] = $footprint->getAllImpactData();
            $this->projects_by_footprint[$footprint->id] = Project::getByFootprint([
                'footprints' => $footprint->id,
                'rand' => true,
                'amount_bigger_than' => 7000
            ]);
            $this->sdg_by_footprint[$footprint->id] = Sdg::getList(['footprint' => $footprint->id]);
        }
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
