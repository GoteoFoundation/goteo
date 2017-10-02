<?
  $cost = $this->cost;
  $form = $this->raw('form');
?>
<div class="panel section-content" data-id="<?= $cost->id ?>">
  <div class="panel-body">
      <div class="row">
        <div class="col-xs-4"><?= $this->form_row($form['amount_' . $cost->id]) ?></div>
        <div class="col-xs-8"><?= $this->form_row($form['type_' . $cost->id]) ?></div>
      </div>
      <?= $this->form_row($form['cost_' . $cost->id]) ?>
      <?= $this->form_row($form['description_' . $cost->id]) ?>
  </div>
</div>
