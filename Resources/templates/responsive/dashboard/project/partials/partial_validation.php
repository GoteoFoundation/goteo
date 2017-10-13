<?php
$validation = $this->validation;
$zone = $this->zone;
if(!$validation) return;
if(!$zone) return;

if($validation->{$zone} < 100) {
    $desc = $this->text('guide-project-error-mandatories');

    $err = '<a href="#" onclick="$(\'form[name=autoform]\').attr(\'action\', \'/dashboard/project/' . $validation->project . '/' .$zone .'?validate\').submit();return false;">' . $this->text('project-validation-error-' . $validation->errors[$zone]) .'</a>';
} else {
    $desc = $this->text('guide-project-success-noerrors');
}


?>

<div class="validation-widget">
    <div class="desc">
        <h4><?= $desc ?></h4>

        <?php if($err): ?>
            <ul class="list-unstyled">
                <li><?= $err ?></li>
            </ul>
        <?php endif ?>
    </div>
</div>

