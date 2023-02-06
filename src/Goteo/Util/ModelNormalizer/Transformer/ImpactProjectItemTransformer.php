<?php

namespace Goteo\Util\ModelNormalizer\Transformer;

class ImpactProjectItemTransformer extends AbstractTransformer
{
    protected $keys = ['impactProjectId', 'impact_item', 'value' ];

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
        return $this->model->getValue();
    }

    public function getActions()
    {
        if(!$u = $this->getUser()) return [];
        $project = $this->model->getProject()->id;
        $id = $this->model->getId();
        $ret = [
            'edit' => "/dashboard/project/$project/impact_item_project/$id/edit",
        ];
        return $ret;
    }
}
