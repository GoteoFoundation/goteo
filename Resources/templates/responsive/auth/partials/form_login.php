
    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <input type="text" class="form-control" placeholder="<?= $this->text('login-recover-email-field') ?>" name="username" value="<?= $this->username ?>" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <input type="password" class="form-control" placeholder="<?= $this->text('login-access-password-field') ?>" name="password" required>
        </div>
    </div>



    <div class="form-group">
        <div class="col-md-10 col-md-offset-1">
            <button type="submit" class="btn btn-block btn-success"><?= $this->text('login-title') ?></button>
        </div>
        <div class="col-md-10 col-md-offset-1 standard-margin-top">
            <a data-toggle="modal" data-target="#myModal" href=""><?= $this->text('login-recover-label') ?></a>
        </div>
        <div class="col-md-10 col-md-offset-1 standard-margin-top">
            <a href="/signup?return=<?= urlencode($this->raw('return')) ?>" ><?= $this->text('login-new-user-label') ?></a>
        </div>
    </div>
