<?php
  $reward = $this->reward;
  $form = $this->raw('form');
  $unlimited = (int)$form['units_' . $reward->id]->vars['value'] == 0;
?>
<div class="panel section-content" data-id="<?= $reward->id ?>">
  <div class="panel-body reward-item">
      <div class="row">
        <div class="amount"><?= $this->form_row($form['amount_' . $reward->id]) ?></div>
        <div class="units"><?= $this->form_row($form['units_' . $reward->id],['disabled' => $unlimited]) ?></div>
        <div class="unlimited form-group">
          <label for="unlimited_<?= $reward->id ?>"><?= $this->text('project-reward-unlimited') ?></label>
          <div class="material-switch">
              <input id="unlimited_<?= $reward->id ?>" type="checkbox"<?= $unlimited ? ' checked="true"' : '' ?>>
              <label for="unlimited_<?= $reward->id ?>" class="label-cyan"></label>
          </div>
        </div>
      </div>
      <?= $this->form_row($form['reward_' . $reward->id]) ?>
      <?= $this->form_row($form['description_' . $reward->id]) ?>
     <div class="remove"><?= $this->form_row($form['remove_' . $reward->id], [],  true) ?></div>
  </div>
</div>
