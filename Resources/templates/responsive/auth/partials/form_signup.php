<?php
$login_link = $this->login_link ? $this->raw('login_link') : ('/login?return=' . urlencode($this->raw('return')) );
$err = $this->errors ? $this->errors : [];
$suggest = $this->suggest ? $this->suggest : [];
?>

<div class="form-group<?= $err['name'] ? ' has-error' : '' ?>">
    <div class="col-md-10 col-md-offset-1">
        <input type="text" class="form-control" placeholder="<?= $this->text('register-name-field') ?>" name="name" value="<?= $this->name ?>" required>
        <?= ($err['name'] ? '<span class="help-block info-block">' . $err['name'] . '</span>' : '') ?>
    </div>
</div>

<div class="form-group<?= $err['email'] ? ' has-error' : '' ?>">
    <div class="col-md-10 col-md-offset-1">
        <input type="email" class="form-control" placeholder="<?= $this->text('register-email-field') ?>" name="email" value="<?= $this->email ?>" required>
        <?= ($err['email'] ? '<span class="help-block info-block">' . $err['email'] . '</span>' : '') ?>
    </div>
</div>

<div class="form-group<?= $err['userid'] ? ' has-error' : '' ?>">
    <div class="col-md-10 col-md-offset-1">
        <input type="text" class="form-control<?= $err['userid'] ? ' user-edited' : '' ?>" placeholder="<?= $this->text('register-id-field') ?>" name="userid" value="<?= $this->userid ?>" required>
        <?= ($err['userid'] ? '<span class="help-block info-block">' . $err['userid'] . '</span>' : '') ?>
        <?= ($suggest ? '<span class="help-block suggest-block">' . $this->text('login-alternate-ids') . ': <a href="#" class="userid-suggestion">' . implode('</a>, <a href="#" class="userid-suggestion">', $suggest) . '</a></span>' : '') ?>
    </div>
</div>

<div class="form-group<?= $err['password'] ? ' has-error' : '' ?>">
    <div class="col-md-10 col-md-offset-1">
        <input type="password" class="form-control" placeholder="<?= $this->text('register-password-field') ?>" name="password" value="" required>
    </div>
</div>

<div class="form-group<?= $err['password'] ? ' has-error' : '' ?>">
    <div class="col-md-10 col-md-offset-1">
        <input type="password" class="form-control" placeholder="<?= $this->text('register-password-confirm-field') ?>" name="rpassword" value="" required>
        <?= ($err['password'] ? '<span class="help-block info-block">' . $err['password'] . '</span>' : '') ?>
    </div>
</div>

<div class="form-group<?= $err['register_accept'] ? ' has-error' : '' ?>">
    <div class="col-md-10 col-md-offset-1">
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
</div>

<div class="form-group">
    <div class="col-md-10 col-md-offset-1">
        <button type="submit" id="register_continue" <?= $this->register_accept ? '' : ' disabled="disabled"' ?> class="btn btn-success"><?= $this->text('register-button-title') ?></button>
        <a class="btn btn-link" href="<?= $login_link ?>"><?= $this->text('register-question') ?></a>
    </div>
</div>

