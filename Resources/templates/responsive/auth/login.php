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
				<h2 class="col-md-offset-1 padding-bottom-6">Iniciar sesión</h2>

                <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

				<form class="form-horizontal" role="form" method="POST" action="/login?return=<?= $return ?>">

					<div class="form-group">
						<div class="col-md-10 col-md-offset-1">
							<input type="text" class="form-control" placeholder="Correo electrónico o nombre de usuario" name="username" value="<?= $this->username ?>" required>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-10 col-md-offset-1">
							<input type="password" class="form-control" placeholder="Contraseña" name="password" required>
						</div>
					</div>



					<div class="form-group">
						<div class="col-md-10 col-md-offset-1">
							<button type="submit" class="btn btn-block btn-success">Iniciar sesión</button>
						</div>
                        <div class="col-md-10 col-md-offset-1 standard-margin-top">
                            <a data-toggle="modal" data-target="#myModal" href="">¿Olvidó su contraseña?</a>
                        </div>
                        <div class="col-md-10 col-md-offset-1 standard-margin-top">
                            <a href="/signup?return=<?= $return ?>">¿Crear un nuevo usuario?</a>
						</div>
					</div>

					<hr>
					<div class="form-group margin-top-7">
						<div class="col-md-10 col-md-offset-1">
							<a href="/login/facebook?return=<?= $return ?>" class="btn btn-block btn-social btn-facebook">
			    				<i class="fa fa-facebook"></i> Iniciar sesión con Facebook
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
        <h4 class="modal-title" id="myModalLabel">Ingresa la dirección de correo electrónico que usaste para registrarte y te enviaremos un enlace para restablecer tu contraseña.</h4>
      </div>
      <div class="modal-body">
        <form>
        	<input type="email" class="form-control" placeholder="Correo electrónico" name="email" value="">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success">Enviar</button>
      </div>
    </div>
  </div>
</div>

<?php $this->replace() ?>
