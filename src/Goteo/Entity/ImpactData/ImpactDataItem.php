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

use Goteo\ImpactItem\ImpactItem;
use Goteo\Model\ImpactData;

class ImpactDataItem
{
    private ImpactData $impactData;
    private ImpactItem $impactItem;

    public function getImpactData(): ImpactData
    {
        return $this->impactData;
    }

    public function setImpactData(ImpactData $impactData): ImpactDataItem
    {
        $this->impactData = $impactData;
        return $this;
    }

    public function getImpactItem(): ImpactItem
    {
        return $this->impactItem;
    }

    public function setImpactItem(ImpactItem $impactItem): void
    {
        $this->impactItem = $impactItem;
    }
}
