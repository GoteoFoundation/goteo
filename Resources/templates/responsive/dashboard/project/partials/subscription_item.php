<?php
$id = $this->subscription->id;
$form = $this->raw('form');
?>
<div class="panel section-content" data-id="<?= $id ?>">
    <div class="panel-body reward-item">
        <div class="row">
            <div class="amount"><?= $this->form_row($form["amount_$id"]) ?></div>
        </div>
    </div>
</div>