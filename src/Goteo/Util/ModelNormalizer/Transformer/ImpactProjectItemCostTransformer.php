<?php

namespace Goteo\Util\ModelNormalizer\Transformer;

class ImpactProjectItemCostTransformer extends AbstractTransformer
{
    protected $keys = ['cost_id', 'cost_name'];

    public function getCostId() {
        return $this->model->getCost()->id;
    }

    public function getCostName() {
        return $this->model->getCost()->cost;
    }

    public function getActions()
    {
        $model = $this->model;
        $project = $model->getImpactProjectItem()->getProject()->id;
        $impactProjectItem = $model->getImpactProjectItem()->getId();
        $cost = $this->model->getCost()->id;

        return [
            'delete' => "/dashboard/project/$project/impact/impact_item/$impactProjectItem/cost/$cost/delete"
        ];
    }
}
