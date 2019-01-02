<?php

$this->layout('pool/layout');

$this->section('dashboard-content-pool');

?>

<div class="pool-container">

    <?= $this->supply('invest-share-message', $this->insert('pool/partials/invest-share-message')) ?>

    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>


	<h3 class="clearfix padding-bottom-6"><?= $this->text($this->type.'-invest-spread-header') ?></h3>

	<div class="row">
		<div class="col-sm-6 margin-2">
			<a href="<?= $this->facebook_url ?>" class="btn btn-block btn-social btn-facebook">
    			<i class="fa fa-facebook"></i> <?= $this->text('spread-facebook') ?>
  			</a>
  		</div>
  		<div class="col-sm-6 margin-2">
			<a href="<?= $this->twitter_url ?>" class="btn btn-block btn-social btn-twitter">
    			<i class="fa fa-twitter"></i> <?= $this->text('spread-twitter') ?>
  			</a>
  		</div>

		</div>

</div>

<?php $this->replace() ?>
