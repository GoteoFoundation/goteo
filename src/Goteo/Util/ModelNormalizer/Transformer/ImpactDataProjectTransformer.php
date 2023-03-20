<?php

namespace Goteo\Util\ModelNormalizer\Transformer;

class ImpactDataProjectTransformer extends AbstractTransformer
{
    protected $keys = ['impact_data', 'estimation', 'source', 'dataValue'];

    public function getImpactDataId(): string
    {
        return $this->model->getImpactData()->title;
    }

    public function getEstimation(): string
    {
        return amount_format($this->model->getEstimationAmount());
    }

    public function getDataValue(): string
    {
        $impactData = $this->model->getImpactData();
        $unit = $impactData->data_unit;

        return $this->model->getData() . " (" . $unit . ")"
            ;
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
