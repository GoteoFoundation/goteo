<?php

$err = $this->errors ? $this->errors : [];
$suggest = $this->suggest ? $this->suggest : [];
?>

<div class="form-group<?= $err['name'] ? ' has-error' : '' ?>">
    <label for="name"><?= $this->t('register-name-field') ?></label>
    <input id="name" type="text" class="form-control" name="name" value="<?= $this->name ?>" required>
    <?= ($err['name'] ? '<span class="help-block info-block">' . $err['name'] . '</span>' : '') ?>
</div>

<div class="form-group<?= $err['email'] ? ' has-error' : '' ?>">
    <label for="email">
        <?= $this->text('register-email-field') ?>
    </label>
    <input id="email" type="email" class="form-control" name="email" value="<?= $this->email ?>" required>
        <?= ($err['email'] ? '<span class="help-block info-block">' . $err['email'] . '</span>' : '') ?>
</div>

<div class="form-group<?= $err['userid'] ? ' has-error' : '' ?>">
    <label for="userid">
        <?= $this->t('register-id-field') ?>
    </label>
    <input id="userid" type="text" class="form-control<?= $err['userid'] ? ' user-edited' : '' ?>" name="userid" value="<?= $this->userid ?>" required>
        <?= ($err['userid'] ? '<span class="help-block info-block">' . $err['userid'] . '</span>' : '') ?>
        <?= ($suggest ? '<span class="help-block suggest-block">' . $this->text('login-alternate-ids') . ': <a href="#" class="userid-suggestion">' . implode('</a>, <a href="#" class="userid-suggestion">', $suggest) . '</a></span>' : '') ?>
</div>

<div class="form-group<?= $err['password'] ? ' has-error' : '' ?>">
    <label for="password">
        <?= $this->t('register-password-field') ?>
    </label>
    <input id="password" type="password" class="form-control" name="password" value="" required>
</div>

<div class="form-group<?= $err['password'] ? ' has-error' : '' ?>">
    <label for="rpassword">
        <?= $this->t('register-password-confirm-field') ?>
    </label>
    <input id="rpassword" type="password" class="form-control" placeholder="<?= $this->text('register-password-confirm-field') ?>" name="rpassword" value="" required>
    <?= ($err['password'] ? '<span class="help-block info-block">' . $err['password'] . '</span>' : '') ?>
</div>

<div class="form-group<?= $err['register_accept'] ? ' has-error' : '' ?>">
    <div class="checkbox">
        <label>
            <input type="checkbox" id="newsletter_accept" class="no-margin-checkbox big-checkbox" name="newsletter_accept"<?= $this->newsletter_accept ? ' checked="checked"' : '' ?>>
            <p class="label-checkbox">
                <?= $this->text('login-register-accept-newsletter') ?>
            </p>
        </label>
        <?= ($err['register_accept'] ? '<span class="help-block info-block">' . $err['register_accept'] . '</span>' : '') ?>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" id="register_accept" class="no-margin-checkbox big-checkbox" name="register_accept"<?= $this->register_accept ? ' checked="checked"' : '' ?>>
            <p class="label-checkbox">
            <?= $this->text('login-register-conditions') ?>
            </p>
        </label>
        <?= ($err['register_accept'] ? '<span class="help-block info-block">' . $err['register_accept'] . '</span>' : '') ?>
    </div>
</div>

<div class="form-group">
    <button type="submit" id="register_continue" <?= $this->register_accept ? '' : ' disabled="disabled"' ?> class="btn btn-success"><?= $this->text('register-button-title') ?></button>
</div>

