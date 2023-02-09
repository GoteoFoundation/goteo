<?php

namespace Goteo\Util\ModelNormalizer\Transformer;

class ImpactDataProjectCostTransformer extends AbstractTransformer
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
        $project = $model->getProject()->id;
        $impactData = $model->getImpactData()->id;
        return [
            'delete' => "/dashboard/project/$project/impact/impact_data/$impactData/impact_items"
        ];
    }
}
