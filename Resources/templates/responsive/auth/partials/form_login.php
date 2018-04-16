<?php
$signup_link = $this->signup_link ? $this->raw('signup_link') : '/signup?return=' . urlencode($this->raw('return'));
?>
    <div class="form-group">
        <input type="text" class="form-control" placeholder="<?= $this->text('login-recover-email-field') ?>" name="username" value="<?= $this->username ?>" required>
    </div>

    <div class="form-group">
        <input type="password" class="form-control" placeholder="<?= $this->text('login-access-password-field') ?>" name="password" required>
    </div>

    <div class="checkbox form-group">
        <label>
          <input type="checkbox" name="rememberme" value="1"> <?= $this->text('regular-remember-me') ?>
        </label>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-block btn-success"><?= $this->text('login-title') ?></button>
    </div>

    <div class="form-group">
        <a data-toggle="modal" data-target="#myModal" href=""><?= $this->text('login-recover-label') ?></a>
    </div>

    <div class="form-group">
        <a href="<?= $signup_link ?>" ><?= $this->text('login-new-user-label') ?></a>
    </div>

