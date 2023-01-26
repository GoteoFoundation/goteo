<?php

namespace Goteo\ImpactItem;

class ImpactItemConversionTip
{
    private ImpactItem $impactItem;
    private string $rateTipDescription;
    private sting $reference;

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

    public function getReference(): sting
    {
        return $this->reference;
    }

    public function setReference(sting $reference): ImpactItemConversionTip
    {
        $this->reference = $reference;
        return $this;
    }



}
