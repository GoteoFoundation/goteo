<?php
?>
<div class="col-md-4 col-sm-4 col-xs-12">
    <h4>Calculadora</h4>
    <article class="card-impact-calculator">
        <div class="row">
            <div class="col-md-2 col-xs-2 col-sm-4">
                <i class="icon icon-calculator"></i>
            </div>
            <div class="col-md-10 col-xs-10 col-sm-8">
                <h3>Calcula el impacto de tu donación</h3>
                <p>Descubre el alcance de tu apoyo</p>
                <div class="button-modal hidden-sm hidden-md hidden-lg">
                    <button class="btn" data-toggle="modal" data-target="#impact-calculator-modal">
                        Haz tu cálculo aquí
                    </button>
                </div>
            </div>
        </div>
        <div class="button-modal hidden-xs">
            <button class="btn" data-toggle="modal" data-target="#impact-calculator-modal">
                Haz tu cálculo aquí
            </button>
        </div>
    </article>
</div>

<?php
$count = count($this->impactDataProjectList);
$amountPerImpactDataProject = 100.0 / $count;
?>
<div class="modal fade" id="impact-calculator-modal" tabindex="-1" role="dialog" aria-labelledby="admin-modal-label" data-impact-data-project-count=<?= $count ?>>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
        <label for="modal-budget">Cantidad de tu aportación</label>
        <div class="modal-input">
            <i class="icon icon-2x icon-money-bag"></i>
            <input type="text" id="modal-budget" name="modal-budget" width="100%" placeholder="100 €">
        </div>
        <p>Con tu donación contribuyes a poder hacer posible:</p>
      </div>
      <div class="modal-body">
        <?php
        foreach($this->impactDataProjectList as $impactDataProject):
          $impactData = $impactDataProject->getImpactData();
          ?>
          <div class="impact-data-info" data-id="<?= $impactData->id ?>" data-operation-type="<?= $impactData->operation_type ?>" data-estimation-amount="<?= $impactDataProject->getEstimationAmount() ?>" data-data="<?= $impactDataProject->getData() ?>">
              <h3><?= $impactData->title ?></h3>
              <div class="row">
                  <div class="col-md-4 col-sm-4 col-xs-6 detail">
                      <h4><?= $impactData->data_unit ?></h4>
                      <p><i class="icon icon-2x icon-person"></i> <?= $impactDataProject->getData() ?></p>
                  </div>
                  <div class="col-md-4 col-sm-4 col-xs-6 impact">
                      <h4><?= $this->t('regular-impact') ?></h4>
                      <?php
                        $calculation = (  (float) ($impactDataProject->getData() / $impactDataProject->getEstimationAmount() ) * $amountPerImpactDataProject);
                        $calulationFormatted = number_format($calculation, 2)
                      ?>
                      <p id="result-impact-data-<?= $impactData->id ?>"class="result" data-result-msg="<?= $impactData->result_msg ?>"><?= vsprintf($impactData->result_msg, [number_format($amountPerImpactDataProject, 2), $calulationFormatted ]) ?></p>
                  </div>
              </div>
          </div>
      <?php endforeach; ?>
      </div>
      <div class="modal-footer">
          <h2>De tu aportación de 100€ hacienda te devolverá 80€</h2>
      </div>
    </div>
  </div>
</div>
