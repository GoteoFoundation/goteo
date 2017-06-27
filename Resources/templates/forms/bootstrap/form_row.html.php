<?php
$label_view = $label_position === 'none' ? '' : $view['form']->label($form);
?>
<div class="form-group<?= $row_class ? " $row_class" : '' ?>">
    <?= $label_position === 'right' ? '' : $label_view ?>
    <?= $view['form']->errors($form) ?>
    <?= $view['form']->widget($form) ?>
    <?= $label_position === 'right' ? $label_view : '' ?>
</div>
