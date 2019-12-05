<?php
  $form = $this->raw('form');
?>
<div class="panel section-content">
  <div class="panel-body reward-item">
    <div class="row">
      <div class="amount">
        <?= $this->form_row($form[$this->question->id . "_typeofquestion"]) ?>
      </div>
      <div class="form-group">
        <div class="input-wrap">
          <?= $this->form_row($form[$this->question->id . "_required"]) ?>
        </div>
      </div>
    </div>
    
    <?= $this->form_row($form[$this->question->id . "_question"]) ?>
    
    <div class="remove"><?= $this->form_row($form[$this->question->id . "_remove"], [],  true) ?></div>
  </div>
</div>
