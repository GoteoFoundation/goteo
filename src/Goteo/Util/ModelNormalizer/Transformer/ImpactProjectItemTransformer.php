<?php

namespace Goteo\Util\ModelNormalizer\Transformer;

class ImpactProjectItemTransformer extends AbstractTransformer
{
    protected $keys = ['impact_item', 'value' ];

    public function getImpactProjectId(): int
    {
        return $this->model->getId();
    }

    public function getImpactDataId(): int
    {
        return $this->model->getImpactItem()->getId();
    }

    public function getImpactItemName(): string
    {
        return $this->model->getImpactItem()->getName();
    }

    public function getImpactValue()
    {
        $impactItem = $this->model->getImpactItem();
        $impactData = $this->model->getRelatedImpactData();
        $unit = $impactData->data_unit;
        return $this->model->getValue() . " ($unit)";
    }

    public function getActions()
    {
        if(!$u = $this->getUser()) return [];
        $project = $this->model->getProject()->id;
        $id = $this->model->getId();
        if ($this->model->getRelatedImpactData())
            $impactData = $this->model->getRelatedImpactData()->id;

        return [
            'edit' => "/dashboard/project/$project/impact/impact_data/$impactData/impact_item/$id/edit",
            'costs' => "/dashboard/project/$project/impact/impact_data/$impactData/impact_item/$id/costs",
            'delete' => "/dashboard/project/$project/impact/impact_data/$impactData/impact_item/$id/delete"
        ];

    }
}
