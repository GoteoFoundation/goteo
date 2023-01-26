<?php

namespace Goteo\ImpactItem;

use Goteo\Model\Project;

class ImpactProjectItem
{
    private int $id;
    private ImpactItem $impactItem;
    private Project $project;
    private string $value;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ImpactProjectItem
    {
        $this->id = $id;
        return $this;
    }

    public function getImpactItem(): ImpactItem
    {
        return $this->impactItem;
    }

    public function setImpactItem(ImpactItem $impactItem): ImpactProjectItem
    {
        $this->impactItem = $impactItem;
        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): ImpactProjectItem
    {
        $this->project = $project;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): ImpactProjectItem
    {
        $this->value = $value;
        return $this;
    }
}
