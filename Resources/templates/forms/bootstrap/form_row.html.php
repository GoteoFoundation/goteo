<?php
$label_view = $label_position === 'none' ? '' : $view['form']->label($form);

?>
<div class="form-group<?= $row_class ? " $row_class" : '' ?><?= (count($errors) > 0) ? ' has-error' : '' ?>">

    <?= $label_position === 'right' ? '' : $label_view ?>

    <?php if(!$no_input_wrap): ?><div class="input-wrap"><?php endif ?>

    <?= $view['form']->errors($form) ?>
    <?= $view['form']->widget($form) ?>

    <?php if(!$no_input_wrap): ?></div><?php endif ?>

    <?= $label_position === 'right' ? $label_view : '' ?>

</div>
