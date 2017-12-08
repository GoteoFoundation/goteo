<div class="row" <?php echo $view['form']->block($form, 'widget_container_attributes') ?>>
<?php foreach ($form as $child): ?>
    <div class="<?= $form->vars['wrap_class'] ?>">
        <label>
        <?php echo $view['form']->widget($child) ?>
        <span><?php echo $view->escape(false !== $choice_translation_domain ? $view['translator']->trans($child->vars['label'], array(), $choice_translation_domain) : $label) ?></span>
        </label>
    </div>
<?php endforeach ?>
</div>
