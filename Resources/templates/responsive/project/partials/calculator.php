<?php
?>
<div class="col-md-4 col-sm-4 col-xs-12">
    <h4><?= $this->t('project-calculator-title') ?></h4>
    <article class="card-impact-calculator">
        <div class="row">
            <div class="col-md-2 col-xs-2 col-sm-4">
                <i class="icon icon-calculator"></i>
            </div>
            <div class="col-md-10 col-xs-10 col-sm-8">
                <h3><?= $this->t('project-calculator-card-title') ?></h3>
                <p><?= $this->t('project-calculator-card-description') ?></p>
                <div class="button-modal hidden-sm hidden-md hidden-lg">
                    <button class="btn" data-toggle="modal" data-target="#impact-calculator-modal">
                        <?= $this->t('project-calculator-card-button') ?>
                    </button>
                </div>
            </div>
        </div>
        <div class="button-modal hidden-xs">
            <button class="btn" data-toggle="modal" data-target="#impact-calculator-modal">
                <?= $this->t('project-calculator-card-button') ?>
            </button>
        </div>
    </article>
</div>

<?php
$count = count($this->impactDataProjectList);
$amountPerImpactDataProject = number_format(100.0 / $count, 2);
?>
<div class="modal fade" id="impact-calculator-modal" tabindex="-1" role="dialog" aria-labelledby="admin-modal-label" data-impact-data-project-count=<?= $count ?>>
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
        <label for="modal-budget"><?= $this->t('project-calculator-modal-budget-label') ?></label>
        <div class="modal-input">
            <i class="icon icon-2x icon-money-bag"></i>
            <input type="text" id="modal-budget" name="modal-budget" width="100%" placeholder="100 €">
        </div>
        <p><?= $this->t('project-calculator-modal-budget-impact-description') ?></p>
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
                      <p id="result-impact-data-<?= $impactData->id ?>"class="result" data-result-msg="<?= $impactData->result_msg ?>"><?= str_replace(['%amount', '%result'], [number_format($amountPerImpactDataProject, 2), $calulationFormatted ], $impactData->result_msg) ?></p>
                  </div>
              </div>
          </div>
      <?php endforeach; ?>
      </div>
      <div class="modal-footer">
          <h2><?= $this->t('project-calculator-modal-footer-description') ?></h2>
      </div>
    </div>
  </div>
</div>
