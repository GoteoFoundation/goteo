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

use Goteo\ImpactItem\Cost;

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
