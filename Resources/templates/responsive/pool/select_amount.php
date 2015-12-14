<?php
$this->layout('layout', [
    'bodyClass' => '',
    'title' => $this->text('meta-title-pool-method'),
    'meta_description' => $this->text('invest-method-title')
    ]);

$this->section('content');

?>

<?= $this->insert('pool/partials/steps_bar') ?>

<div class="container">

	<div class="row row-form">
			<div class="panel panel-default invest-container">
				<div class="panel-body">

                    <h2 class="col-sm-offset-1 padding-bottom-2"><?= $this->text('pool-recharge-title') ?></h2>

                    <?= $this->insert('pool/partials/amount_box') ?>

					</form>
				</div>
			</div>

	</div>

</div>


<?php $this->replace() ?>
