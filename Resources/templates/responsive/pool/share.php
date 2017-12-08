<?php

$this->layout('pool/layout');

$this->section('dashboard-content-pool');

?>

<div class="pool-container">

	<h2 class="padding-bottom-2"><?= $this->text('pool-share-title') ?></h2>
	<div class="reminder">
        <?= $this->text('pool-invest-ok') ?>
        <div class="row spacer">

    		<div class="col-sm-6 margin-2">
    				<a href="/dashboard/wallet/certificate" class="text-decoration-none" >
    					<button type="button" class="btn btn-block btn-info" value=""><i class="icon icon-certificate"></i> <?= $this->text('pool-button-download-certificate') ?></button>
    	  			</a>
    	  	</div>

    	  	<div class="col-sm-6 margin-2">
    				<a href="/dashboard/wallet" class="text-decoration-none" >
    					<button type="button" class="btn btn-block btn-success" value=""><i class="icon icon-wallet"></i> <?= $this->text('dashboard-menu-pool') ?></button>
    	  			</a>
    	  	</div>

      	</div>
	</div>

    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>


	<h3 class="clearfix padding-bottom-6"><?= $this->text('pool-invest-spread-header') ?></h3>

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
