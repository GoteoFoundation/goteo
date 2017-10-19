<?php

$errors = $this->a('errors');

?>
<div class="form-group<?= $errors['name'] ? ' has-error' : '' ?>">
    <label><?= $this->text('regular-name') ?></label>
    <input type="text" class="form-control" placeholder="<?= $this->text('regular-name') ?>" name="name" value="<?= $this->name ?>">
    <?= ($errors['name'] ? '<span class="help-block">' . nl2br($errors['name']) . '</span>' : '') ?>
</div>
<div class="form-group<?= $errors['email'] ? ' has-error' : '' ?>">
    <label><?= $this->text('regular-email') ?></label>
    <input type="email" class="form-control" placeholder="<?= $this->text('regular-email') ?>" name="email" value="<?= $this->email ?>" required>
    <?= ($errors['email'] ? '<span class="help-block">' . nl2br($errors['email']) . '</span>' : '') ?>
</div>
<div class="form-group<?= $errors['register_accept'] ? ' has-error' : '' ?>">
    <div class="checkbox">
        <label>
            <input type="checkbox" id="register_accept" class="no-margin-checkbox big-checkbox" name="register_accept"<?= $this->register_accept ? ' checked="checked"' : '' ?>>
            <p class="label-checkbox">
            <?= $this->text('login-accept-conditions') ?>
            </p>
        </label>
        <?= ($errors['register_accept'] ? '<span class="help-block info-block">' . $errors['register_accept'] . '</span>' : '') ?>
    </div>
</div>
