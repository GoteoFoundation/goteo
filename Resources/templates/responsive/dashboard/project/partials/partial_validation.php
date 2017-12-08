<?php
$validation = $this->validation;
$zone = $this->zone;
if(!$validation) return;
if(!$zone) return;

$errors = [];
if($validation->{$zone} < 100) {
    $desc = $this->text('guide-project-error-mandatories');

    foreach($validation->errors[$zone] as $error) {
        $errors[] = '<a href="#" onclick="$(\'form[name=autoform]\').attr(\'action\', \'/dashboard/project/' . $validation->project . '/' .$zone .'?validate\').submit();return false;">' . $this->text('project-validation-error-' . $error) .'</a>';
    }
} else {
    $desc = $this->text('guide-project-success-noerrors');
}


?>

<div class="validation-widget">
    <div class="desc">
        <h4><?= $desc ?></h4>

        <?php if($errors): ?>
            <ul class="list-unstyled">
            <?php foreach($errors as $err): ?>
                <li><?= $err ?></li>
            <?php endforeach ?>
            </ul>
        <?php endif ?>
    </div>
</div>

