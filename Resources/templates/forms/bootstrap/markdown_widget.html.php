<?php if($disabled): ?>
    <div class="form-control-static disabled"><?= $value ?></div>
<?php else: ?>
<textarea <?php echo $view['form']->block($form, 'widget_attributes', ['required' => false]) ?>><?php echo $view->escape($value) ?></textarea>
<?php endif ?>
