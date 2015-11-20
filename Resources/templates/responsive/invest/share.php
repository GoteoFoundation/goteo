<?php

$this->layout('layout', [
    'bodyClass' => '',
    'title' => 'Share the project :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

?>

<?= $this->insert('invest/partials/project_info') ?>

<?= $this->insert('invest/partials/steps_bar') ?>

<div class="container">

	<div class="row row-form">
			<div class="panel panel-default invest-container">
				<div class="panel-body">
					<h2 class="col-sm-offset-1 padding-bottom-2"><?= $this->text('invest-share-title') ?></h2>
					<div class="reminder col-md-10 col-md-offset-1">
        		    <?= $this->text('project-invest-ok') ?>
      				</div>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

                    <div class="row">

						<h3 class="col-md-offset-1 col-md-10 clearfix padding-bottom-6"><?= $this->text('project-spread-header') ?></h3>
					
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

			  		<hr class="share">

			  		<h3 class="col-md-offset-1 standard-margin-top sm-display-none padding-bottom-6" ><?= $this->text('project-spread-widget') ?></h3>


			  		<!-- Widget code -->
					<div class="row standard-margin-top sm-display-none">
						<div class="col-md-5 col-md-offset-1">
							<?= $this->raw('widget_code') ?>
							<h4 class="embed-code"><?= $this->text('project-spread-embed_code') ?></h4>
							<textarea class="widget-code" onclick="this.focus();this.select()" readonly="readonly" ><?= $this->widget_code ?></textarea>
						</div>
						<div class="col-md-5">
							<?= $this->raw('widget_code_investor') ?>
							<h4 class="embed-code"><?= $this->text('project-spread-embed_code') ?></h4>
							<textarea class="widget-code" onclick="this.focus();this.select()" readonly="readonly" ><?= $this->widget_code_investor ?></textarea>
						</div>
					</div>

					<hr class="share hidden-xs">

					<div class="row">

						<div class="col-sm-6 col-sm-offset-3 margin-2">
								<a href="<?= SITE_URL ?>" class="text-decoration-none" >
									<button type="button" class="btn btn-block btn-success" value=""><?= $this->text('goteo-return-button') ?></button>
					  			</a>
					  	</div>

				  	</div>
				</div>
			</div>
	</div>
	
</div>

<?php $this->replace() ?>
