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

use Goteo\Model\Footprint;

class ImpactItemFootprint
{
    private ImpactItem $impactItem;
    private Footprint $footprint;

    public function getImpactItem(): ImpactItem
    {
        return $this->impactItem;
    }

    public function setImpactItem(ImpactItem $impactItem): ImpactItemFootprint
    {
        $this->impactItem = $impactItem;
        return $this;
    }

    public function getFootprint(): Footprint
    {
        return $this->footprint;
    }

    public function setFootprint(Footprint $footprint): ImpactItemFootprint
    {
        $this->footprint = $footprint;
        return $this;
    }
}
