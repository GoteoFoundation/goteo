<div class="material-switch">
    <input type="checkbox"
        <?php echo $view['form']->block($form, 'widget_attributes', ['type' => 'checkbox']) ?>
        <?php if (strlen($value) > 0): ?> value="<?php echo $view->escape($value) ?>"<?php endif ?>
        <?php if ($checked): ?> checked="checked"<?php endif ?>
    />

    <label for="<?= $id ?>" class="label-<?= $color ?>"></label>
</div>
