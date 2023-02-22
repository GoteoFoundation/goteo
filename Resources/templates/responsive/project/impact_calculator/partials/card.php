<?php
    $footprint = $this->footprint;
    $impactData = $this->impactData;
?>

<article id="card-impact-data-<?= $impactData->id ?>" data-impact-data="<?= $impactData->id ?>" data-footprint="<?= $footprint ?>" data-operation="<?= $impactData->operation_type ?>" class="card col-md-4 col-sm-4 footprint-<?= $footprint ?>">

    <header class="card-header" title="<?= $impactData->title ?>">
        <h1><?= $impactData->title ?></h1>
    </header>
    <div class="card-body">
        <!--
        <div class="info">
            <i class="fa fa-info"
               data-html="true"
               data-container="body"
               data-toggle="tooltip"
               title=""
               data-original-title="">
            </i>
        </div>
        -->

        <label for="budget"><?= $this->t('regular-budget') ?>
            <div class="card-input">
                <i class="icon icon-money-bag"></i>
                <input id="form_<?= $footprint ?>_<?= $impactData->id ?>_estimated_amount" type="number" name="form[<?= $footprint ?>][<?= $impactData->id ?>][estimated_amount]">
            </div>
        </label>


        <label for="form_<?= $footprint ?>_<?= $impactData->id ?>_data"><?= $impactData->data_unit ?>
            <div class="card-input">
                <i class="icon icon-medal"></i>
                <input id="form_<?= $footprint ?>_<?= $impactData->id ?>_data" name="form[<?= $footprint ?>][<?= $impactData->id ?>][data]" type="number">
            </div>
        </label>

        <div>
            <h4><?= $this->text('regular-impact') ?></h4>
            <div class="card-input row">
                <div class="col-xs-4">
                <i class="icon icon-classroom"></i></div>
                <div class="col-xs-8"><p id="card_<?= $footprint ?>_<?= $impactData->id ?>_result_msg" data-text="<?= $impactData->result_msg ?>"></p></div>
            </div>
        </div>

        <div class="form-group activate-impact-data">
            <label for="form_<?= $footprint ?>_<?= $impactData->id ?>_active">
                <?= $this->t('project-impact-calculator-activate-indicator') ?>
            </label>
            <div class="material-switch">
                <input type="checkbox" class="form" id="form_<?= $footprint ?>_<?= $impactData->id ?>_active" name="form[<?= $footprint ?>][<?= $impactData->id ?>][active]">
                <label for="form_<?= $footprint ?>_<?= $impactData->id ?>_active"></label>
            </div>

        </div>
    </div>
</article>

