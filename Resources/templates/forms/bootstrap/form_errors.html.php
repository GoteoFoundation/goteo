<?php if (count($errors) > 0): ?>
    <?php foreach ($errors as $error): ?>
        <span class="help-block"><?php echo $view->escape(false !== $translation_domain ? $view['translator']->trans($error->getMessage(), array(), $translation_domain) : $error->getMessage()) ?></span>
    <?php endforeach; ?>
<?php endif ?>
