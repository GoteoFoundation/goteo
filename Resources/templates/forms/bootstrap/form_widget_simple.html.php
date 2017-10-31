<?php if($pre_addon || $post_addon): ?>
<div class="input-group">
  <?php if($pre_addon): ?>
    <div class="input-group-addon<?= $disabled ? ' disabled' : '' ?>"><?= $pre_addon ?></div>
  <?php endif ?>
<?php endif ?>
<input type="<?php echo isset($type) ? $view->escape($type) : 'text' ?>" <?php echo $view['form']->block($form, 'widget_attributes', array('type' => $type)) ?><?php if (!empty($value) || is_numeric($value)): ?> value="<?php echo $view->escape($value) ?>"<?php endif ?> />
<?php if($pre_addon || $post_addon): ?>
  <?php if($post_addon): ?>
    <div class="input-group-addon"><?= $post_addon ?></div>
  <?php endif ?>
</div>
<?php endif ?>
