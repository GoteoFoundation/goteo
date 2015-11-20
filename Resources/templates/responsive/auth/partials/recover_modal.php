<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div id="modal-content" class="modal-content">
      <div class="modal-header">

        <?= $this->error ?>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $this->text('login-recover-modal-text') ?></h4>
      </div>
      <div class="modal-body">
        <form name="password-recover-form" id="password-recover-form" action="">
            <input type="text" class="form-control" placeholder="<?= $this->text('login-recover-email-field') ?>" id="password-recover-email" name="password-recover-email" value="" required>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn-password-recover" class="btn btn-success"><?= $this->text('login-recover-modal-button') ?></button>
      </div>
    </div>
  </div>
</div>
