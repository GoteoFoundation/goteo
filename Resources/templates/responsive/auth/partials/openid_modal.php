<!-- Modal -->
<div class="modal fade" id="openIdModal" tabindex="-1" role="dialog" aria-labelledby="openIdLabel">
  <div class="modal-dialog" role="document">
    <div id="modal-content" class="modal-content">
      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="openIdLabel"><?= $this->text('login-signin-openid') ?></h4>
      </div>
      <div class="modal-body">
        <form name="openid-form" id="openid-form" action="">
            <input type="text" class="form-control" placeholder="" id="openid" name="openid" value="" required>
        </form>
      </div>
      <div class="modal-footer">
        <a id="openid-link" href="#">
          <button type="button" id="btn-openid" class="btn btn-success"><?= $this->text('login-signin-openid-go') ?></button>
        </a>
      </div>
    </div>
  </div>
</div>