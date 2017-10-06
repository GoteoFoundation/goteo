<?php
  $reward = $this->reward;
  $form = $this->raw('form');
?>
<div class="panel section-content" data-id="<?= $reward->id ?>">
  <div class="panel-body reward-item">
      <div class="row">
        <div class="col-xs-6 col-xxs-12 amount"><?= $this->form_row($form['amount_' . $reward->id]) ?></div>
        <div class="col-xs-6 col-xxs-12 units"><?= $this->form_row($form['units_' . $reward->id]) ?></div>
      </div>
      <?= $this->form_row($form['reward_' . $reward->id]) ?>
      <?= $this->form_row($form['description_' . $reward->id]) ?>
     <div class="remove"><?= $this->form_row($form['remove_' . $reward->id], [],  true) ?></div>
  </div>
</div>
