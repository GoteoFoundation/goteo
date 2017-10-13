<?php
  $id = $this->reward->id;
  $form = $this->raw('form');
  $unlimited = (int)$form["units_$id"]->vars['value'] == 0;
?>
<div class="panel section-content" data-id="<?= $id ?>">
  <div class="panel-body reward-item">
      <div class="row">
        <div class="amount"><?= $this->form_row($form["amount_$id"]) ?></div>
        <div class="units"><?= $this->form_row($form["units_$id"]) ?></div>
        <div class="unlimited form-group">
          <label for="unlimited_<?= $id ?>"><?= $this->text('project-reward-unlimited') ?></label>
          <div class="material-switch">
              <input id="unlimited_<?= $id ?>"<?= $form["units_$id"]->vars['disabled'] ? ' disabled' :'' ?> type="checkbox"<?= $unlimited ? ' checked="true"' : '' ?>>
              <label for="unlimited_<?= $id ?>" class="label-cyan"></label>
          </div>
        </div>
      </div>
      <?= $this->form_row($form["reward_$id"]) ?>
      <?= $this->form_row($form["description_$id"]) ?>
     <div class="remove"><?= $this->form_row($form["remove_$id"], [],  true) ?></div>
  </div>
</div>
