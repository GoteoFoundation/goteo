<div class="row" <?= $view['form']->block($form, 'widget_container_attributes') ?>>
<?php foreach ($form as $child):
    $val = false !== $choice_translation_domain ? $view['translator']->trans($child->vars['label'], array(), $choice_translation_domain) : $label;
?>
    <div class="<?= $form->vars['wrap_class'] ?>">
        <label class="<?= $attr['label_class'] ?>">
        <?= $view['form']->widget($child,['required' => false]) ?>
        <span><?= $form->vars['choice_label_escape'] ? $view->escape($val) : $val ?></span>
        </label>
    </div>
<?php endforeach ?>
</div>
