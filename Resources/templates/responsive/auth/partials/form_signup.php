<div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <input type="text" class="form-control" placeholder="<?= $this->text('register-name-field') ?>" name="username" value="<?= $this->username ?>" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <input type="email" class="form-control" placeholder="<?= $this->text('register-email-field') ?>" name="email" value="<?= $this->email ?>" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <input type="email" class="form-control" placeholder="<?= $this->text('register-email-confirm-field') ?>" name="remail" value="<?= $this->remail ?>" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <input type="text" class="form-control" placeholder="<?= $this->text('register-id-field') ?>" name="userid" value="<?= $this->userid ?>" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <input type="password" class="form-control" placeholder="<?= $this->text('register-password-field') ?>" name="password" value="" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <input type="password" class="form-control" placeholder="<?= $this->text('register-password-confirm-field') ?>" name="rpassword" value="" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="register_accept" class="no-margin-checkbox big-checkbox" name="remember">
                        <p class="label-checkbox">
                        <?= $this->text('login-register-conditions') ?>
                        </p>
                </label>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <button type="submit" id="register_continue" disabled="disabled" class="btn btn-success"><?= $this->text('register-button-title') ?></button>
            <a class="btn btn-link" href="/login?return=<?= urlencode($this->raw('return')) ?>"><?= $this->text('register-question') ?></a>
        </div>
    </div>

