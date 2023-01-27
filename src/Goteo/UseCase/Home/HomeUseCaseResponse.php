<?php

namespace Goteo\UseCase\Home;

use Goteo\Model\Banner;
use Goteo\Model\Footprint;
use Goteo\Model\Home;
use Goteo\Model\ImpactData;
use Goteo\Model\Node;
use Goteo\Model\Project;
use Goteo\Model\Sponsor;
use Goteo\Model\Stories;
use Goteo\Util\Stats\Stats;

class HomeUseCaseResponse
{
    public const LIMIT_ADD_JS = 12;
    public const LIMIT = 24;

    private array $projects;
    private int $totalProjects;
    private array $stories;
    private array $channels;
    private array $banners;
    private Stats $stats;
    private array $footprints;
    private array $homeItems;
    private array $sponsors;
    private array $projectsByFootprint;
    private array $sdgByFootprint = [];
    private array $footprintImpactData = [];

    /**
     * @param Project[] $projects
     * @param int $totalProjects
     * @param Stories[] $stories
     * @param Node[] $channels
     * @param Banner[] $banners
     * @param Stats $stats
     * @param Footprint[] $footprints
     * @param Home[] $homeItems
     * @param Sponsor[] $sponsors
     * @param Project[] $projectsByFootprint
     * @param Project[] $sdgByFootprint
     * @param ImpactData[] $footprintImpactData
     */
    public function __construct(
        array $projects,
        int $totalProjects,
        array $stories,
        array $channels,
        array $banners,
        Stats $stats,
        array $footprints,
        array $homeItems,
        array $sponsors,
        array $projectsByFootprint,
        array $sdgByFootprint,
        array $footprintImpactData
    ) {
        $this->projects = $projects;
        $this->totalProjects = $totalProjects;
        $this->stories = $stories;
        $this->channels = $channels;
        $this->banners = $banners;
        $this->stats = $stats;
        $this->footprints = $footprints;
        $this->homeItems = $homeItems;
        $this->sponsors = $sponsors;
        $this->projectsByFootprint = $projectsByFootprint;
        $this->sdgByFootprint = $sdgByFootprint;
        $this->footprintImpactData = $footprintImpactData;
    }

    public function getLimit(): int
    {
        return self::LIMIT;
    }

    /**
     * @return Project[]
     */
    public function getProjects(): array
    {
        return $this->projects;
    }

    public function getTotalProjects(): int
    {
        return $this->totalProjects;
    }

    /**
     * @return Stories[]
     */
    public function getStories(): array
    {
        return $this->stories;
    }

    /**
     * @return Node[]
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    /**
     * @return Banner[]
     */
    public function getBanners(): array
    {
        return $this->banners;
    }

    public function getStats(): Stats
    {
        return $this->stats;
    }

    /**
     * @return Footprint[]
     */
    public function getFootprints(): array
    {
        return $this->footprints;
    }

    /**
     * @return Home[]
     */
    public function getHomeItems(): array
    {
        return $this->homeItems;
    }

    /**
     * @return Sponsor[]
     */
    public function getSponsors(): array
    {
        return $this->sponsors;
    }

    /**
     * @return ImpactData[]
     */
    public function getFootprintImpactData(): array
    {
        return $this->footprintImpactData;
    }

    /**
     * @return Project[]
     */
    public function getProjectsByFootprint(): array
    {
        return $this->projectsByFootprint;
    }

    /**
     * @return Project[]
     */
    public function getSdgByFootprint(): array
    {
        return $this->sdgByFootprint;
    }
}
