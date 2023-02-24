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

class ImpactDataProject {
    private ImpactData $impactData;
    private Project $project;
    private int $estimationAmount = 0;
    private int $data = 0;

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

    public function getEstimationAmount(): int
    {
        return $this->estimationAmount;
    }

    public function setEstimationAmount(int $estimationAmount): ImpactDataProject
    {
        $this->estimationAmount = $estimationAmount;
        return $this;
    }

    public function getData(): ?int
    {
        return $this->data;
    }

    public function setData(int $data): ImpactDataProject
    {
        $this->data = $data;
        return $this;
    }
}
