<?php
    $footprint = $this->footprint;
    $impactData = $this->impactData;
?>

<article class="card col-md-4 col-sm-4 footprint-<?= $footprint ?>">

    <header class="card-header">
        <h1><?= $impactData->title ?></h1>
    </header>
    <div class="card-body">

        <div class="info">
            <i class="fa fa-info"
               data-html="true"
               data-container="body"
               data-toggle="tooltip"
               title=""
               data-original-title="
                    <span >
                        <h4 style='font-size: 16px; font-weight:bold;'>CREACIÓN DE EMPLEO</h4>
                        <p>Según el Instituto Nacional de Estadística (INE) el coste medio de un trabajador a jornada completa en España es de 31.150€/año.</`>
                    </span>">
            </i>
        </div>

        <label for="budget"><?= $this->t('regular-budget') ?>
            <div class="card-input">
                <i class="icon icon-money-bag"></i>
                <input id="form_<?= $footprint ?>_<?= $impactData->id ?>_estimated_amount" type="numbre" name="form[<?= $footprint ?>][<?= $impactData->id ?>][estimated_amount]">
            </div>
        </label>


        <label for="number_personas"><?= $impactData->data_unit ?></label>
        <div class="card-input">
            <i class="icon icon-medal"></i>
            <input type="form_<?= $footprint ?>_<?= $impactData->id ?>_data" name="form[<?= $footprint ?>][<?= $impactData->id ?>][data]">
        </div>

        <div>
            <h4><?= $this->text('regular-impact') ?></h4>
            <div class="card-input row">
                <div class="col-xs-4">
                <i class="icon icon-classroom"></i></div>
                <div class="col-xs-8"><p data-text="<?= $impactData->result_msg ?>"></p></div>
            </div>
        </div>

        <div class="form-group activate-impact-data">
            <div class="material-switch">
                <input type="checkbox" class="form" id="form_<?= $footprint ?>_<?= $impactData->id ?>_active" name="form[<?= $footprint ?>][<?= $impactData->id ?>][active]">
                <label for="form_<?= $footprint ?>_<?= $impactData->id ?>_active"></label>
            </div>
            <label for="form_<?= $footprint ?>_<?= $impactData->id ?>_active">
                ACTIVAR
            </label>
        </div>
    </div>
</article>

