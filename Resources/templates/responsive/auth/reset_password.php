<?php

$this->layout('auth/layout', [
    'title' => $this->text('reset-password-title'),
    'description' => $this->text('reset-password-title')
    ]);

$this->section('inner-content');
?>
    <h2 class="col-md-offset-1 padding-bottom-6"><?= $this->text('reset-password-title') ?> </h2>

    <form class="form-horizontal" role="form" method="POST" action="/password-reset?return=<?= urlencode($this->raw('return')) ?>">

        <div class="form-group">
            <div class="col-md-10 col-md-offset-1">
                <input type="password" class="form-control" placeholder="<?= $this->text('reset-password-old-password') ?>" name="password" required>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-10 col-md-offset-1">
                <input type="password" class="form-control" placeholder="<?= $this->text('reset-password-new-password') ?>" name="rpassword" required>
            </div>
        </div>


        <div class="form-group">
            <div class="col-md-10 col-md-offset-1">
                <button type="submit" class="btn btn-block btn-success"><?= $this->text('reset-password-save') ?></button>
            </div>
        </div>
    </form>

<?php $this->replace() ?>
