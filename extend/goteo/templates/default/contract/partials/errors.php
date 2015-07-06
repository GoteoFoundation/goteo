<?php

$contract = $this->contract;
$step = $this->step;
$step_errors = count($contract->errors[$step]);
$total_errors = 0;

foreach ($contract->errors as $st => $errors) {
    $total_errors += count($errors);
}

// Para que salte al campo
/*<!-- <a href="#<?= $id ?>" onclick="document.getElementById('<?= $id ?>').focus(); return false;"> -->*/

?>
<div>

    <?php if ($step != 'final') : ?>
    <p><?= $this->text('form-errors-info', $total_errors, $step_errors) ?></p>

        <?php if (!empty($contract->errors[$step])) : ?>
        <ul class="sf-footer-errors">
            <?php foreach ($contract->errors[$step] as $id => $error) : ?>
            <li>
                <?= $error ?>
            </li>
            <?php endforeach ?>
        </ul>
        <?php endif ?>
    <?php else : ?>
        <p><?= $this->text('form-errors-total', $total_errors) ?></p>
        <?php foreach ($contract->errors as $st => $errors)  :
            if (!empty($errors)) : ?>
            <h4 class="title"><?= $this->text('contract-step-' . $st) ?></h4>
            <ul class="sf-footer-errors">
            <?php foreach ($errors as $id => $error) : ?>
                <li><?= $error ?></li>
            <?php endforeach ?>
            </ul>
        <?php endif;
            endforeach ?>
    <?php endif ?>

</div>
