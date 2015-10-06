<?php

$this->layout('layout', [
    'bodyClass' => '',
    'title' => 'Share the project :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

?>
<div class="container">

	<div class="row row-form">
			<div class="panel panel-default share">
				<div class="panel-body">
					<div class="alert alert-success col-md-10 col-md-offset-1" role="alert">
        		    <?= $this->text('project-invest-ok') ?>
      				</div>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

					<h3 class="col-md-offset-1 padding-bottom-6"><?= $this->text('project-spread-header') ?></h3>
					
					<div class="row">
						<div class="col-sm-5 col-md-offset-1 margin-2">
							<a href="<?= $this->facebook_url ?>" class="btn btn-block btn-social btn-facebook">
				    			<i class="fa fa-facebook"></i> <?= $this->text('spread-facebook') ?>
				  			</a>
				  		</div>
				  		<div class="col-sm-5 margin-2">
							<a href="<?= $this->twitter_url ?>" class="btn btn-block btn-social btn-twitter">
				    			<i class="fa fa-facebook"></i> <?= $this->text('spread-twitter') ?>
				  			</a>
				  		</div>
			  		</div>

			  		<hr class="share">

			  		<h3 class="col-md-offset-1 standard-margin-top sm-display-none padding-bottom-6" ><?= $this->text('project-spread-widget') ?></h3>


			  		<!-- Widget code -->
					<div class="row standard-margin-top sm-display-none">
						<div class="col-md-5 col-md-offset-1">
						<?= $this->raw('widget_code') ?>
						</div>
						<div class="col-md-5">
						<?= $this->raw('widget_code_investor') ?>
						</div>
					</div>
				</div>
			</div>
	</div>
	<?= $this->insert('invest/partials/steps_bar') ?>
</div>

<?php $this->replace() ?>
