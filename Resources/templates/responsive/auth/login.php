<?php

$this->layout("layout", [
    'bodyClass' => '',
    'title' => 'Iniciar sesión',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

$return = urlencode($this->get_query('return'));
?>
<div class="container">

	<div class="row row-form">
		<div class="panel panel-default panel-form">
			<div class="panel-body">
				<h2 class="col-md-offset-1 padding-bottom-6"><?= $this->text('login-title') ?></h2>

                <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

				<form class="form-horizontal" role="form" method="POST" action="/login?return=<?= $return ?>">

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
                            <a href="/signup?return=<?= $return ?>"><?= $this->text('login-new-user-label') ?></a>
						</div>
					</div>

					<hr>
					<div class="form-group margin-top-7">
						<div class="col-md-10 col-md-offset-1">
							<a href="/login/facebook?return=<?= $return ?>" class="btn btn-block btn-social btn-facebook">
			    				<i class="fa fa-facebook"></i> <?= $this->text('login-signin-facebook') ?>
			  				</a>
			  			</div>

			  			<div class="col-md-10 col-md-offset-1 standard-margin-top">
			  				<a href="/login/twitter?return=<?= $return ?>" class="btn btn-social-icon btn-twitter">
		    					<i class="fa fa-twitter"></i>
		  					</a>
			  				<a href="/login/google?return=<?= $return ?>" class="btn btn-social-icon btn-google">
			    				<i class="fa fa-google-plus"></i>
			  				</a>

			  				<a href="/login/Yahoo?return=<?= $return ?>" class="btn btn-social-icon btn-yahoo">
			    				<i class="fa fa-yahoo"></i>
			  				</a>
			  				<a href="/login/linkedin?return=<?= $return ?>" class="btn btn-social-icon btn-linkedin">
			    				<i class="fa fa-linkedin"></i>
			  				</a>
			  				<a href="/login/facebook?return=<?= $return ?>" class="btn btn-social-icon btn-openid">
				    			<i class="fa fa-openid"></i>
				  			</a>
		  				</div>
		  			</div>

				</form>

			</div>
		</div>
	</div>

</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $this->text('login-recover-modal-text') ?></h4>
      </div>
      <div class="modal-body">
        <form>
        	<input type="email" class="form-control" placeholder="<?= $this->text('login-recover-email-field') ?>" name="email" value="">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success"><?= $this->text('login-recover-modal-button') ?></button>
      </div>
    </div>
  </div>
</div>

<?php $this->replace() ?>
