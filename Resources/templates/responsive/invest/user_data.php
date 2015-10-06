<?php

$this->layout('layout', [
    'bodyClass' => '',
    'title' => 'Make sure :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

$invest = $this->invest;

?>
<div class="container">

	<div class="row row-form">
			<div class="panel panel-default make-sure">
				<div class="panel-body">
					<div class="alert alert-success col-md-10 col-md-offset-1" role="alert">
        			<?= $this->text('invest-success-make-sure',$this->project->name) ?>
      				</div>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

                    <form class="form-horizontal" id="make-sure-form" role="form" method="POST" action="/invest/<?= $this->project->id ?>/<?= $this->invest->id ?>">

                        <?= $this->insert('invest/partials/reward_address_form') ?>

						<hr>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1">
								<button type="submit" class="btn btn-block btn-success">Guardar</button>
							</div>
						</div>

					</form>


				</div>
			</div>
	</div>
	<?= $this->insert('invest/partials/steps_bar') ?>
</div>

<?php $this->replace() ?>
