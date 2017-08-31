<?php if (count($errors) > 0): ?>
    <?php foreach ($errors as $error): ?>
        <?php if (!$form->parent): ?>
            <p class="text-danger"><i class="fa fa-frown-o fa-spin"></i> <?= $error->getMessage() ?></p>
        <?php else: ?>
            <span class="help-block"><i class="fa fa-frown-o fa-spin"></i> <?= $error->getMessage() ?></span>
        <?php endif ?>
    <?php endforeach; ?>
<?php endif ?>
