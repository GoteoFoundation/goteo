<?php if (count($errors) > 0): ?>
    <?php foreach ($errors as $error): ?>
        <span class="help-block"><?= $error->getMessage() ?></span>
    <?php endforeach; ?>
<?php endif ?>
