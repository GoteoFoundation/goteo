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
    private const LIMIT = 24;

    private array $projects;
    private int $totalProjects;
    private array $stories;
    private array $channels;
    private array $banners;
    private Stats $stats;
    private array $sponsors;
    private array $footprints;
    private array $homeItems;
    private array $projectsByFootprint;
    private array $sdgByFootprint = [];
    private array $footprintImpactData = [];

    public function __construct()
    {
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

    /**
     * @param Project[]
     */
    public function setProjects(array $projects): self
    {
        $this->projects = $projects;

        return $this;
    }

    public function getTotalProjects(): int
    {
        return $this->totalProjects;
    }

    public function setTotalProjects(int $totalProjects): self
    {
        $this->totalProjects = $totalProjects;

        return $this;
    }

    /**
     * @return Stories[]
     */
    public function getStories(): array
    {
        return $this->stories;
    }

    /**
     * @param Stories[]
     */
    public function setStories(array $stories): self
    {
        $this->stories = $stories;

        return $this;
    }

    /**
     * @return Node[]
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    /**
     * @param Node[]
     */
    public function setChannels(array $channels): self
    {
        $this->channels = $channels;

        return $this;
    }

    /**
     * @return Banner[]
     */
    public function getBanners(): array
    {
        return $this->banners;
    }

    /**
     * @param Node[]
     */
    public function setBanners(array $banners): self
    {
        $this->banners = $banners;

        return $this;
    }

    public function getStats(): Stats
    {
        return $this->stats;
    }

    public function setStats(Stats $stats): self
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * @return Footprint[]
     */
    public function getFootprints(): array
    {
        return $this->footprints;
    }

    /**
     * @param Footprint[]
     */
    public function setFootprints(array $footprints): self
    {
        $this->footprints = $footprints;

        return $this;
    }

    /**
     * @return Home[]
     */
    public function getHomeItems(): array
    {
        return $this->homeItems;
    }

    /**
     * @param Home[]
     */
    public function setHomeItems(array $homeItems): self
    {
        $this->homeItems = $homeItems;

        return $this;
    }

    /**
     * @return Sponsor[]
     */
    public function getSponsors(): array
    {
        return $this->sponsors;
    }

    /**
     * @param Sponsor[]
     */
    public function setSponsors(array $sponsors): self
    {
        $this->sponsors = $sponsors;

        return $this;
    }

    /**
     * @return ImpactData[]
     */
    public function getFootprintImpactData(): array
    {
        return $this->footprintImpactData;
    }

    /**
     * @param ImpactData[]
     */
    public function setFootprintImpactData(array $footprintImpactData): self
    {
        $this->footprintImpactData = $footprintImpactData;

        return $this;
    }

    /**
     * @return Project[]
     */
    public function getProjectsByFootprint(): array
    {
        return $this->projectsByFootprint;
    }

    /**
     * @param Project[]
     */
    public function setProjectsByFootprint(array $projectsByFootprint): self
    {
        $this->projectsByFootprint = $projectsByFootprint;

        return $this;
    }

    /**
     * @return Project[]
     */
    public function getSdgByFootprint(): array
    {
        return $this->sdgByFootprint;
    }

    /**
     * @param Project[]
     */
    public function setSdgByFootprint(array $sdgByFootprint): self
    {
        $this->sdgByFootprint = $sdgByFootprint;

        return $this;
    }
}
