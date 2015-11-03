<?php

$this->layout("layout", [
    'bodyClass' => '',
    'title' => 'Iniciar sesión',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

?>
<div class="container">

	<div class="row row-form">
		<div class="panel panel-default panel-form">
			<div class="panel-body">
				<h2 class="col-md-offset-1 padding-bottom-6"><?= $this->text('login-title') ?></h2>

                <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

				<form class="form-horizontal" role="form" method="POST" action="/login?return=<?= urlencode($this->raw('return')) ?>">

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
                            <a href="/signup?return=<?= urlencode($this->raw('return')) ?>"><?= $this->text('login-new-user-label') ?></a>
						</div>
					</div>

					<hr>
                    <?= $this->insert('auth/partials/social_login') ?>
				</form>

			</div>
		</div>
	</div>

</div>

<?= $this->insert('auth/partials/recover_modal') ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">

$(function(){

	$("#myModal").on('click', '#btn-password-recover', function(){

	   var email=$("#password-recover-email").val();

	   $.ajax({
	          url: "/password-recovery",
	          data: { 'email' : email, 'return' : '<?= urlencode($this->get_query('return')) ?>'  },
	          type: 'post',
	          success: function(result){
	            $("#modal-content").html(result);
	    }});

	});

});

</script>

<?php $this->append() ?>
