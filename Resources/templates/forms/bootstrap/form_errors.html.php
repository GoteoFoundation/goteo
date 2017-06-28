<?php if (count($errors) > 0): ?>
    <?php foreach ($errors as $error): ?>
        <?php if (!$form->parent): ?>
            <p class="text-danger"><?= $error->getMessage() ?></p>
        <?php else: ?>
            <span class="help-block"><?= $error->getMessage() ?></span>
        <?php endif ?>
    <?php endforeach; ?>
<?php endif ?>
