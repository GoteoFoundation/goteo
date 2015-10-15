<?php

$this->layout('layout', [
    'bodyClass' => '',
    'title' => 'Make sure :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

$invest = $this->invest;

?>

<?= $this->insert('invest/partials/project_info') ?>

<div class="container">

	<div class="row row-form">
			<div class="panel panel-default make-sure">
				<div class="panel-body">

                    <?= $this->insert('invest/partials/invest_header_form') ?>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

                    <form class="form-horizontal" id="make-sure-form" role="form" method="POST" action="/invest/<?= $this->project->id ?>/<?= $this->invest->id ?>">

                        <?= $this->insert('invest/partials/invest_address_form') ?>

                        <?= $this->insert('invest/partials/invest_submit_form') ?>

					</form>


				</div>
			</div>
	</div>
	<?= $this->insert('invest/partials/steps_bar') ?>
</div>

<?php $this->replace() ?>
