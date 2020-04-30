<?php //Extract the first title for the alement embebed in the header banner ?>
<?php $terms=$this->channel->getTerms (); ?>

<?php $first_term=current($terms); ?>

<div class="section banner-header">
	<div class="custom-header">
		<div class="pull-left">
			<img src="/assets/img/channel/call/logo_crowdcoop.png" height="30px">
		</div>
		<div class="pull-right">
			<span><?= $this->text('call-header-powered-by') ?></span>
          	<img height="30" src="<?= '/assets/img/goteo-white-green.png' ?>" >
      	</div>
	</div>
	<div class="image">
		<img src="/assets/img/channel/call/terms/header.png" width="1920" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
		<img src="/assets/img/channel/call/terms/header-1400.png" width="1400" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
		<img src="/assets/img/channel/call/terms/header-1051.png" width="1051" class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
		<img src="/assets/img/channel/call/terms/header-750.png" width="750"  class="img-responsive header-default visible-xs">
	</div>

	<div class="banner-info terms">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-7 col-sm-9 col-xs-8">
					<div>
						<span class="title">
						<?= $this->channel->terms_banner_title ?>
						</span>
					</div>
					<div class="description">
						<?= $this->channel->terms_banner_description ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="info terms">
		<div class="container">
			<div class="row">
				<div class="col-md-10 item">
					<h2 class="title" role="button" data-toggle="collapse" href="<?= '#collapse-'.$first_term->id ?>" aria-expanded="true">
						<span class="icon icon-<?= $first_term->icon ?> icon-3x"></span>
						<?= $first_term->title ?>
					</h2>	
				</div>
			</div>
		</div>
	</div>
</div>