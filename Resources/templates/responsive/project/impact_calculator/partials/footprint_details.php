<?php
    $footprint = $this->footprint;
?>

<div class="footprint-details">
    <div class="footprint-dropdown">
        <div>
            <h3>
                <img width="75" src="<?= $this->asset("img/footprint/$footprint->id.svg") ?>">
                <?= $footprint->title ?>
            </h3>
            <p><?= $this->t("project-impact-calculator-footprint-$footprint->id-description") ?></p>
        </div>
        <button class="btn accordion-toggle" data-toggle="collapse" data-target="#collapse-footprint-<?= $footprint->id ?>">
            <span class="icon icon-arrow"></span>
        </button>
    </div>

    <div id="collapse-footprint-<?= $footprint->id ?>" class="collapse">
        <?= $this->insert('project/impact_calculator/partials/card', ['active' => true, 'footprint' => $this->footprint->id]) ?>
        <?= $this->insert('project/impact_calculator/partials/card', ['active' => false]) ?>
    </div>
</div>
