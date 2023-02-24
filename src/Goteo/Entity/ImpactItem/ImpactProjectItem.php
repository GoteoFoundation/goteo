<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Entity\ImpactItem;

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
