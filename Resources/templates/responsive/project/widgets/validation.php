<?php
$init_percent = intval($this->init_percent);
$validation = $this->validation;
$errors = [];
if($validation->errors) {
    $desc = $this->text('project-validation-errors');
    foreach($validation->errors as $err) {
        $errors[] = '<a href="/dashboard/project/' . $validation->project . '/' .$err .'?validate"><i class="fa fa-hand-o-right"></i> ' .$this->text('project-validation-error-' . $err) .'</a>';
    }
}
else {
    $desc = $this->text('project-validation-ok');
}
?>
<div class="validation-widget">
    <div class="percent">
        <?= $this->insert('project/widgets/percent_status', ['percent' => $init_percent]) ?>
    </div>
    <div class="desc">
        <p><?= $desc ?></p>
        <ul class="list-unstyled">
        <?php foreach($errors as $err): ?>
            <li><?= $err ?></li>
        <?php endforeach ?>
        </ul>
        <a class="btn btn-lg btn-fashion<?= $validation->global < 100 ? ' disabled' : '' ?>"><i class="fa fa-paper-plane"></i> <?= $this->text('project-send-review') ?></a>
    </div>
</div>
