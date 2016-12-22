<?php

$this->layout("layout", [
    'bodyClass' => '',
    'title' => $this->text('meta-title-register'),
    'meta_description' => $this->text('meta-description-register')
    ]);

$this->section('content');
?>
<div class="container">

	<div class="row row-form">
			<div class="panel panel-default panel-form">
				<div class="panel-body">
					<h2 class="col-md-offset-1 padding-bottom-6"> <?= $this->text('register-form-title') ?></h2>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

					<form class="form-horizontal" role="form" method="post" action="/signup?return=<?= urlencode($this->raw('return')) ?>">

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

						<hr>

                        <?= $this->insert('auth/partials/social_login') ?>

					</form>


				</div>
			</div>
	</div>
</div>

<?= $this->insert('auth/partials/openid_modal') ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type='text/javascript'>

$(function() {

  $('#register_accept').change(function() {
  	    $('#register_continue').attr('disabled', !this.checked);

  });

  $('#openid').change(function() {
  	    $('#openid-link').attr('href', '/login/openid?return=<?= urlencode($this->raw('return')) ?>&u='+$(this).val());

  });

})

</script>

<?php $this->append() ?>
