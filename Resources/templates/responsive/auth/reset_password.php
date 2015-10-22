<?php

$this->layout("layout", [
    'bodyClass' => '',
    'title' => 'Establecer contraseña :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');
?>
<div class="container">

	<div class="row row-form">
			<div class="panel panel-default panel-form">
				<div class="panel-body">
					<h2 class="col-md-offset-1 padding-bottom-6">Establecer nueva contraseña</h2>

					<form class="form-horizontal" role="form" method="POST" action="/password-reset?return=<?= $this->return ?>">

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="password" class="form-control" placeholder="Tu nueva contraseña" name="password" required>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<input type="password" class="form-control" placeholder="Vuelve a escribir tu nueva contraseña" name="rpassword" required>
							</div>
						</div>


						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<button type="submit" class="btn btn-block btn-success">Guardar</button>
							</div>
						</div>
					</form>								
				</div>
			</div>
		
	</div>
</div>

<?php $this->replace() ?>