<?php
$impact_data_project = $this->impact_data_project;
$impactDataProjectByFootprint = $this->impactDataProjectByFootprint;
$impactProjectItemList = $this->impactProjectItemList;

if (!empty($impactDataProjectByFootprint)):
?>
<div class="slider-footprint col-md-4 col-sm-4 col-xs-12">
    <?php foreach($this->footprints as $footprint):
        if (!empty($impactDataProjectByFootprint[$footprint->id])):
        ?>
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
                            <h5><?= $impactData->title ?></h5>
                            <div class="info" style="position:absolute; right: 1rem; top: 1rem; background: transparent ">
                                <button style="background: transparent;border: none;" type="button" data-toggle="modal" data-target="#impact-data-project-modal-<?= $impactData->id ?>">
                                    <i class="fa fa-info"></i>
                                </button>
                            </div>
                            <p><?= $this->text('project-impact-calculator-card-impact-description', [amount_format($estimatedAmount), "$data  $dataUnit"]) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </article>
        </div>
        <?php endif; ?>
    <?php endforeach;?>
</div>

<?php foreach($this->footprints as $footprint): ?>
    <?php foreach ($impactDataProjectByFootprint[$footprint->id] as $impactDataProject): ?>
        <?php
            $impactData = $impactDataProject->getImpactData();
        ?>
        <div class="modal fade impact-item-modal" id="impact-data-project-modal-<?= $impactData->id ?>" tabindex="-1" role="dialog" aria-labelledby="impact-item-modal-label">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
                        <h3><?= $this->text('project-calculator-modal-impact-item-list', $impactData->title) ?></h3>
                    </div>
                    <div class="modal-body">
                        <?php
                        foreach($this->impactProjectItemList[$impactData->id] as $impactProjectItem):
                            $impactItem = $impactProjectItem->getImpactItem();
                            ?>
                            <div class="impact-data-details">
                                <h3><?= $impactItem->getName() ?></h3>
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-xs-6 detail">
                                        <h4><?= $impactData->data_unit ?></h4>
                                        <p><i class="icon icon-2x icon-person"></i> <?= $impactProjectItem->getValue() ?></p>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-6 impact">
                                        <h4><?= $this->t('project-calculator-modal-impact-item-amount') ?></h4>
                                        <p><?= $impactProjectItem->getCostAmounts() ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>

<?php endif; ?>
