<?php

$this->layout('layout', [
    'bodyClass' => '',
    'title' => $this->text('meta-title-pool-share'),
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

?>

<?= $this->insert('pool/partials/steps_bar') ?>

<div class="container">

	<div class="row row-form">
			<div class="panel panel-default invest-container">
				<div class="panel-body">
					<h2 class="col-sm-offset-1 padding-bottom-2"><?= $this->text('pool-share-title') ?></h2>
					<div class="reminder col-md-10 col-md-offset-1">
        		    <?= $this->text('pool-invest-ok') ?>
        		    <div class="row spacer">

						<div class="col-sm-5 margin-2">
								<a href="/dashboard/wallet/certificate" class="text-decoration-none" >
									<button type="button" class="btn btn-block btn-info" value=""><?= $this->text('pool-button-download-certificate') ?></button>
					  			</a>
					  	</div>

					  	<div class="col-sm-5 margin-2">
								<a href="/dashboard/wallet" class="text-decoration-none" >
									<button type="button" class="btn btn-block btn-success" value=""><?= $this->text('dashboard-menu-pool') ?></button>
					  			</a>
					  	</div>

				  	</div>
      				</div>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

                    <div class="row">

						<h3 class="col-md-offset-1 col-md-10 clearfix padding-bottom-6"><?= $this->text('pool-invest-spread-header') ?></h3>
					
					</div>
					<div class="row">
						<div class="col-sm-5 col-md-offset-1 margin-2">
							<a href="<?= $this->facebook_url ?>" class="btn btn-block btn-social btn-facebook">
				    			<i class="fa fa-facebook"></i> <?= $this->text('spread-facebook') ?>
				  			</a>
				  		</div>
				  		<div class="col-sm-5 margin-2">
							<a href="<?= $this->twitter_url ?>" class="btn btn-block btn-social btn-twitter">
				    			<i class="fa fa-twitter"></i> <?= $this->text('spread-twitter') ?>
				  			</a>
				  		</div>
				  		
			  		</div>

					<hr class="share hidden-xs">

					<div class="row">

						

				  	</div>
				</div>
			</div>
	</div>
	
</div>

<?php $this->replace() ?>
