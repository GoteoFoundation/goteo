<?php

$this->layout("layout", [
    'bodyClass' => '',
    'title' => 'Registrar :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');
?>
<div class="container">

	<div class="row row-form">
			<div class="panel panel-default panel-form">
				<div class="panel-body">
					<h2 class="col-md-offset-1 padding-bottom-6">Registrar</h2>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

					<form class="form-horizontal" role="form" method="POST" action="">

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="input" class="form-control" placeholder="Nombre" name="nombre">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="email" class="form-control" placeholder="Correo electrónico" name="email" value="">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="email" class="form-control" placeholder="Vuelve a escrbir tu correo electrónico" name="email" value="">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="input" class="form-control" placeholder="Elige tu identificador en Goteo" name="id_publico">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="password" class="form-control" placeholder="Contraseña" name="password">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="password" class="form-control" placeholder="Vuelve a escribir tu contraseña" name="password">
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember">
											<p style="font-size:0.8em;">
											Al registrarte, confirmas aceptación de nuestros <a href="/legal/terms">términos de uso</a>, <a href="/legal/privacy">política de privacidad</a> y política de cookies.
											</p>
									</label>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<button type="submit" class="btn btn-success">Regístrate</button>
								<a class="btn btn-link" href="/login?return=<?= urlencode($this->get_query('return')) ?>">¿Ya estás registrado?</a>
							</div>
						</div>

						<hr>
						<div class="form-group margin-top-7">
							<div class="col-md-10 col-md-offset-1">
								<a class="btn btn-block btn-social btn-facebook">
				    				<i class="fa fa-facebook"></i> Registrarse con Facebook
				  				</a>
				  			</div>

				  			<div class="col-md-10 col-md-offset-1 standard-margin-top">
				  				<a class="btn btn-social-icon btn-twitter">
			    					<i class="fa fa-twitter"></i>
			  					</a>
				  				<a class="btn btn-social-icon btn-google">
				    				<i class="fa fa-google-plus"></i>
				  				</a>

				  				<a class="btn btn-social-icon btn-yahoo">
				    				<i class="fa fa-yahoo"></i>
				  				</a>
				  				<a class="btn btn-social-icon btn-linkedin">
				    				<i class="fa fa-linkedin"></i>
				  				</a>
				  				<a class="btn btn-social-icon btn-openid">
				    				<i class="fa fa-openid"></i>
				  				</a>
			  				</div>
			  			</div>
					</form>


				</div>
			</div>
	</div>
</div>

<?php $this->replace() ?>
