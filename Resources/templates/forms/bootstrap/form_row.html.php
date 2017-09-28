<?php
$label_view = $label_position === 'none' ? '' : $view['form']->label($form);

if($type === 'title') {
    echo '<h4>' . $label_view . '</h4>';
    return;
}
?>
<div class="form-group<?= $row_class ? " $row_class" : '' ?><?= (count($errors) > 0) ? ' has-error' : '' ?>">

    <?= $label_position === 'right' ? '' : $label_view ?>

    <?php if(!$no_input_wrap): ?><div class="input-wrap"><?php endif ?>


    <?= $view['form']->errors($form) ?>

  <?php if($type === 'markdown'): ?>
      <div class="markdown">
  <?php endif ?>

    <?= $view['form']->widget($form) ?>

    <?php if($label_position !== 'right' && $attr['help']): ?>
        <div class="help-text"><?= $attr['help'] ?></div>
    <?php endif ?>

    <?php if(!$no_input_wrap): ?></div><?php endif ?>

    <?= $label_position === 'right' ? $label_view : '' ?>
    <?php if($label_position === 'right' && $attr['help']): ?>
        <div class="help-text"><?= $attr['help'] ?></div>
    <?php endif ?>

  <?php if($type === 'markdown'): ?>
      </div>
  <?php endif ?>

</div>
