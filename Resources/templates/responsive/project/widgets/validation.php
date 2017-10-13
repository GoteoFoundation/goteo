<?php
$init_percent = intval($this->init_percent);
$validation = $this->validation;
$errors = [];
if($validation->global < 100) {
    $desc = $this->text('project-validation-errors');
    foreach($validation->errors as $type => $errs) {
        foreach($errs as $err) {
            $errors[] = '<a href="/dashboard/project/' . $validation->project . '/' .$type .'?validate">' .$this->text('project-validation-error-' . $err) .'</a>';
        }
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
        <h4><?= $desc ?></h4>

        <?php if($errors): ?>
            <ul class="list-unstyled">
            <?php foreach($errors as $err): ?>
                <li><?= $err ?></li>
            <?php endforeach ?>
            </ul>
        <?php endif ?>
        <a href="/dashboard/project/<?= $validation->project ?>/apply" class="btn btn-lg btn-fashion apply-project<?= $validation->global < 100 ? ' disabled' : '' ?>"><i class="fa fa-paper-plane"></i> <?= $this->text('project-send-review') ?></a>
    </div>
</div>
