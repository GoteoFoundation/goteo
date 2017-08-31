<div class="row" <?php echo $view['form']->block($form, 'widget_container_attributes') ?>>
<?php foreach ($form as $child): ?>
    <div class="col-sm-4 col-xs-6">
        <?php echo $view['form']->widget($child) ?>
        <?php echo $view['form']->label($child, null, array('translation_domain' => $choice_translation_domain)) ?>
    </div>
<?php endforeach ?>
</div>
