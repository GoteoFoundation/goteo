<?php

$this->layout('dashboard/layout', [
    'bodyClass' => 'dashboard',
    'title' => 'Make sure :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('dashboard-content');

?>

<?= $this->insert('pool/partials/steps_bar') ?>

<div class="dashboard-content cyan">

	<div class="row">
			<div class="panel panel-default invest-container">
				<div class="panel-body">

                    <h2 class="col-sm-offset-1 padding-bottom-2"><?= $this->text('pool-make-sure-title') ?></h2>

                    <?= $this->insert('pool/partials/invest_header_form') ?>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

                    <form class="form-horizontal" id="make-sure-form" role="form" method="POST" action="/pool/<?= $this->invest->id ?>">

                        <?= $this->supply('invest-form', $this->insert('invest/partials/invest_address_form')) ?>

                        <?= $this->insert('invest/partials/invest_submit_form') ?>

					</form>


				</div>
			</div>
	</div>

</div>

<?php $this->replace() ?>
