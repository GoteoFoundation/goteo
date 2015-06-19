<?php
use Goteo\Library\Text;

$contract = $vars['contract'];
$step = $vars['step'];
$step_errors = count($contract->errors[$step]);
$total_errors = 0;

foreach ($contract->errors as $st => $errors) {
    $total_errors += count($errors);
}

// Para que salte al campo
/*<!-- <a href="#<?php echo $id ?>" onclick="document.getElementById('<?php echo $id ?>').focus(); return false;"> -->*/

?>
<div>

    <?php if ($step != 'final') : ?>
    <p><?php echo Text::get('form-errors-info', $total_errors, $step_errors) ?></p>

        <?php if (!empty($contract->errors[$step])) : ?>
        <ul class="sf-footer-errors">
            <?php foreach ($contract->errors[$step] as $id => $error) : ?>
            <li>
                <?php echo $error ?>
            </li>
            <?php endforeach ?>
        </ul>
        <?php endif ?>
    <?php else : ?>
        <p><?php echo Text::get('form-errors-total', $total_errors) ?></p>
        <?php foreach ($contract->errors as $st => $errors)  :
            if (!empty($errors)) : ?>
            <h4 class="title"><?php echo Text::get('contract-step-'.$st); ?></h4>
            <ul class="sf-footer-errors">
            <?php foreach ($errors as $id => $error) : ?>
                <li><?php echo $error ?></li>
            <?php endforeach; ?>
            </ul>
        <?php endif;
            endforeach; ?>
    <?php endif; ?>

</div>
