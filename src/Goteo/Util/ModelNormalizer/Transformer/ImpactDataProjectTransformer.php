<?php

namespace Goteo\Util\ModelNormalizer\Transformer;

class ImpactDataProjectTransformer extends AbstractTransformer
{
    protected $keys = ['impact_data', 'estimation', 'dataValue'];

    public function getImpactDataId(): string
    {
        return $this->model->getImpactData()->title;
    }

    public function getEstimation(): int
    {
        return $this->model->getEstimationAmount();
    }

    public function getDataValue(): int
    {
        return $this->model->getData();
    }

    public function getActions()
    {
        $model = $this->model;
        $project = $model->getProject()->id;
        $impactData = $model->getImpactData()->id;

        return [
            'list' => "/dashboard/project/$project/impact/impact_data/$impactData/impact_items"
        ];
    }
}
