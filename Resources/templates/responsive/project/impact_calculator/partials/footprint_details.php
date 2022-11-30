<?php
    $footprint = $this->footprint;
?>

<div class="footprint-details">
    <div class="footprint-dropdown">
        <div>
            <h3>
                <img width="75" src="<?= $this->asset("img/footprint/$footprint.svg") ?>">
                Huella Ecológica
            </h3>
            <p>
                Lorem impsum dolor sit amet, consectetur adipiscing elit.
            </p>
        </div>
        <button class="btn accordion-toggle" data-toggle="collapse" data-target="#collapse-footprint-<?= $footprint ?>">
            <span class="icon icon-arrow"></span>
        </button>
    </div>

    <div id="collapse-footprint-<?= $footprint ?>" class="collapse">
        <?= $this->insert('project/impact_calculator/partials/card', ['active' => true, 'footprint' => $this->footprint]) ?>
        <?= $this->insert('project/impact_calculator/partials/card', ['active' => false]) ?>
    </div>
</div>
