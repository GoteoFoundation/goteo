<?
  $cost = $this->cost;
  $translated = $cost->getLang($this->lang);
  $form = $this->raw('form');
?>
<div class="panel section-content" data-id="<?= $cost->id ?>">
  <div class="panel-body<?= $cost->required ? '' : ' lilac' ?>">
    <div class="pull-left" style="width: 85%;">
      <?= $this->form_row($form['cost_' . $cost->id], ['value' => $translated->cost, 'attr' => ['help' => $cost->cost]]) ?>
      <?= $this->form_row($form['description_' . $cost->id], ['value' => $translated->description, 'attr' => ['help' => $cost->description]]) ?>
    </div>
    <div class="pull-right text-right" style="width: 15%;">
      <h4 title="<?= $this->types[$cost->type] ?>">
        <?= amount_format($cost->amount) ?><br>
        <img src="<?= $this->asset('/img/project/needs/'.$cost->type.'.png') ?> ">
      </h4>
    </div>
  </div>
</div>
