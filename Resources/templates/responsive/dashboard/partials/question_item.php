<?php
  $form = $this->raw('form');
?>
<div class="panel section-content">
  <div class="panel-body reward-item">
    <div class="row">
      <div class="form-group col-md-4">
        <div class="input-wrap">
          <?= $this->form_row($form[$this->question->id . "_typeofquestion"]) ?>
        </div>
      </div>
      <div class="form-group col-md-4">
        <div class="input-wrap">
          <?= $this->form_row($form[$this->question->id . "_max_score"]) ?>
        </div>
      </div>
      <div class="form-group col-md-4">
        <div class="input-wrap">
          <?= $this->form_row($form[$this->question->id . "_required"]) ?>
        </div>
      </div>
      <div class="form-group col-md-4">
        <div class="input-wrap">
          <?= $this->form_row($form[$this->question->id . "_hidden"]) ?>
        </div>
      </div>
    </div>

    
    <?= $this->form_row($form[$this->question->id . "_question"]) ?>

    <div class="form-group">
      <div class="input-wrap">
        <?php if ($this->question->type == "choice") : ?>
          <?php foreach ($this->question->vars->choice as $key => $value) : ?>
            <?= $this->form_row($form[$this->question->id] . "_choice_" . $key) ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
    
    <div class="remove"><?= $this->form_row($form[$this->question->id . "_remove"], [],  true) ?></div>
  </div>
</div>

<template id="new_choice_answer" >
  <?= $this->form_row($form[$this->question->id . "_choice_answer"]) ?>
</template>
