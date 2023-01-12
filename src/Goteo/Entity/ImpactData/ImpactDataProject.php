<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Entity\ImpactData;

use Goteo\Model\ImpactData;
use Goteo\Model\Project;
use Goteo\Model\Project\Cost;

class ImpactDataProject {
    private ImpactData $impactData;
    private Project $project;
    private int $value = 0;
    private ?Cost $cost = null;

    public function getImpactData(): ImpactData
    {
        return $this->impactData;
    }

    public function setImpactData(ImpactData $impactData): ImpactDataProject
    {
        $this->impactData = $impactData;
        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): ImpactDataProject
    {
        $this->project = $project;
        return $this;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): ImpactDataProject
    {
        $this->value = $value;
        return $this;
    }

    public function getCost(): ?Cost
    {
        return $this->cost;
    }

    public function setCost(Cost $cost): ImpactDataProject
    {
        $this->cost = $cost;
        return $this;
    }
}
