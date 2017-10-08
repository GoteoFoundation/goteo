<?php
  $cost = $this->cost;
  $form = $this->raw('form');
?>
<div class="panel section-content" data-id="<?= $cost->id ?>">
  <div class="panel-body cost-item<?= $cost->required ? ' lilac' : '' ?>">
      <div class="row">
        <div class="col-xs-6 col-xxs-12 amount"><?= $this->form_row($form['amount_' . $cost->id]) ?></div>
        <div class="col-xs-6 type">
          <img src="<?= $this->asset('/img/project/needs/'.$cost->type.'.png') ?> ">
          <?= $this->form_row($form['type_' . $cost->id]) ?>
        </div>
      </div>
      <?= $this->form_row($form['cost_' . $cost->id]) ?>
      <?= $this->form_row($form['description_' . $cost->id]) ?>
      <div class="row">
        <div class="col-xs-6 col-xxs-12 required"><?= $this->form_row($form['required_' . $cost->id]) ?></div>
        <div class="col-xs-6 col-xxs-12 remove"><?= $this->form_row($form['remove_' . $cost->id], [], true) ?></div>
      </div>
  </div>
</div>
