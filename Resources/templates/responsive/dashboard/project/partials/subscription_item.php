<?php
$id = $this->subscription->id;
$form = $this->raw('form');
?>
<div class="panel section-content" data-id="<?= $id ?>">
    <div class="panel-body subscription-item">
        <div class="row">
            <div class="amount"><?= $this->form_row($form["amount_$id"]) ?></div>
        </div>
        <?= $this->form_row($form["name_$id"]) ?>
        <?= $this->form_row($form["description_$id"]) ?>
        <div class="remove"><?= $this->form_row($form["remove_$id"], [],  true) ?></div>
    </div>
</div>