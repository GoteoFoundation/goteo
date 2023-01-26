<?php

namespace Goteo\ImpactItem;

class ImpactProjectItemCost
{
    private ImpactProjectItem $itemProjectItem;
    private Cost $cost;

    public function getItemProjectItem(): ImpactProjectItem
    {
        return $this->itemProjectItem;
    }

    public function setItemProjectItem(ImpactProjectItem $itemProjectItem): ImpactProjectItemCost
    {
        $this->itemProjectItem = $itemProjectItem;
        return $this;
    }

    public function getCost(): Cost
    {
        return $this->cost;
    }

    public function setCost(Cost $cost): ImpactProjectItemCost
    {
        $this->cost = $cost;
        return $this;
    }
}
