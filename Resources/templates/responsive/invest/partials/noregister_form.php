<div class="form-group<?= $this->errors['name'] ? ' has-error' : '' ?>">
    <div class="col-md-10 col-md-offset-1">
        <label><?= $this->text('regular-name') ?></label>
        <input type="text" class="form-control" placeholder="<?= $this->text('regular-name') ?>" name="name" value="<?= $this->name ?>">
        <?= ($this->errors['name'] ? '<span class="help-block">' . nl2br($this->errors['name']) . '</span>' : '') ?>
    </div>
</div>
<div class="form-group<?= $this->errors['email'] ? ' has-error' : '' ?>">
    <div class="col-md-10 col-md-offset-1">
        <label><?= $this->text('regular-email') ?></label>
        <input type="email" class="form-control" placeholder="<?= $this->text('regular-email') ?>" name="email" value="<?= $this->email ?>" required>
        <?= ($this->errors['email'] ? '<span class="help-block">' . nl2br($this->errors['email']) . '</span>' : '') ?>
    </div>
</div>
<div class="form-group<?= $this->errors['register_accept'] ? ' has-error' : '' ?>">
    <div class="col-md-10 col-md-offset-1">
        <div class="checkbox">
            <label>
                <input type="checkbox" id="register_accept" class="no-margin-checkbox big-checkbox" name="register_accept"<?= $this->register_accept ? ' checked="checked"' : '' ?>>
                <p class="label-checkbox">
                <?= $this->text('login-accept-conditions') ?>
                </p>
            </label>
            <?= ($this->errors['register_accept'] ? '<span class="help-block info-block">' . $this->errors['register_accept'] . '</span>' : '') ?>
        </div>
    </div>
</div>
