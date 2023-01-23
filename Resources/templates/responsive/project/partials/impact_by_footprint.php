<?php
$impact_data_project = $this->impact_data_project;
$impactDataProjectByFootprint = $this->impactDataProjectByFootprint;
?>
<div class="slider-footprint col-md-4 col-sm-4 col-xs-12">
    <?php foreach($this->footprints as $footprint): ?>
        <div class="footprint-info">

            <h4><?= $footprint->title ?></h4>

            <article class="card-impact-by-footprint footprint-<?= $footprint->id ?>">
                <?php foreach ($impactDataProjectByFootprint[$footprint->id] as $impactDataProject):
                    $impactData = $impactDataProject->getImpactData();
                    $estimatedAmount = $impactDataProject->getEstimationAmount();
                    $data = $impactDataProject->getData();
                    $icon = $impactData->icon;
                    $dataUnit = $impactData->data_unit;
                ?>
                    <div class="row">
                        <div class="col-md-2 col-xs-2">
                            <i class="icon icon-<?= $icon ?? "person" ?>"></i>
                        </div>
                        <div class="col-md-9 col-xs-9">
                            <p><b><?= $impactData->title ?></b>  <br>
                                <?= $dataUnit ?> => <?= $data ?> <br>
                                <?= $this->t('regular-budget') ?>: => <?= amount_format($estimatedAmount) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </article>
        </div>
    <?php endforeach;?>
</div>
