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

use Goteo\ImpactItem\sting;

class ImpactItemConversionTip
{
    private ImpactItem $impactItem;
    private string $rateTipDescription;
    private string $reference;

    public function getImpactItem(): ImpactItem
    {
        return $this->impactItem;
    }

    public function setImpactItem(ImpactItem $impactItem): ImpactItemConversionTip
    {
        $this->impactItem = $impactItem;
        return $this;
    }

    public function getRateTipDescription(): string
    {
        return $this->rateTipDescription;
    }

    public function setRateTipDescription(string $rateTipDescription): ImpactItemConversionTip
    {
        $this->rateTipDescription = $rateTipDescription;
        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): ImpactItemConversionTip
    {
        $this->reference = $reference;
        return $this;
    }



}
