<div <?php echo $view['form']->block($form, 'widget_container_attributes') ?>>
    <?php if (!$form->parent && $errors):
        // Due the hack in form_row.html.php we remove already displayed errors in the inputs
        $name = count($errors)>0 && $errors[0]->getOrigin() ? $errors[0]->getOrigin()->getName() : '';
        $values = is_array($form->vars['value']) ? array_keys($form->vars['value']) : [];
        if(!in_array($name, $values)):
        ?>
    <?php echo $view['form']->errors($form) ?>
    <?php endif;endif ?>
    <?php echo $view['form']->block($form, 'form_rows') ?>
    <?php echo $view['form']->rest($form) ?>
</div>
