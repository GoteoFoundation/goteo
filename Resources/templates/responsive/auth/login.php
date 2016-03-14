<?php

$this->layout("layout", [
    'bodyClass' => '',
    'title' => $this->text('login-title'),
    'meta_description' => $this->text('login-title')
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
                            <a href="/signup?return=<?= urlencode($this->raw('return')) ?>" ><?= $this->text('login-new-user-label') ?></a>
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

<?= $this->insert('auth/partials/openid_modal') ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">

$(function(){
    var _get_ajax_password_result = function() {
        var email=$("#password-recover-email").val();

        $.ajax({
            url: "/password-recovery",
            data: { 'email' : email, 'return' : '<?= urlencode($this->raw('return')) ?>'  },
            type: 'post',
            success: function(result){
                $("#modal-content").html(result);
            }
        });
   };

   $("#myModal").on('keypress', "#password-recover-email", function (e) {
        if (e.keyCode == 10 || e.keyCode == 13) {
            e.preventDefault();
            _get_ajax_password_result();
            return false;
        }
    });

    $('#myModal').on('shown.bs.modal', function () {
        $('#myModal input:first').focus();
    });

	$("#myModal").on('click', '#btn-password-recover', function(){
	   _get_ajax_password_result();
	});

	$('#openid').change(function() {
  	    $('#openid-link').attr('href', '/login/openid?return=<?= urlencode($this->raw('return')) ?>&u='+$(this).val());

  	});

});

</script>

<?php $this->append() ?>
