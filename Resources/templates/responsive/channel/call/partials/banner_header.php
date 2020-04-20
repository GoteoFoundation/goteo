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
		<?php $header_image=$this->channel->getBannerHeaderImage(); ?>
		<img src="<?= $header_image->getLink(1920, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
		<img src="<?= $header_image->getLink(1400, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
		<img src="<?= $header_image->getLink(1051, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
		<img src="<?= $header_image->getLink(550, 600, true) ?>" class="img-responsive visible-xs">
	</div>

	<div class="banner-info">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-7 col-sm-9 col-xs-8">
					<div>
						<span class="title">
						Cooperativismo en Catalunya
						</span>
					</div>
					<div class="description">
						Espacio digital para la creación y consolidación de proyectos cooperativos
					</div>
					<a href="<?= $banner->url ?>" class="btn btn-yellow scroller"><i class="icon icon-plus icon-2x">		
						</i><?= $this->text('landing-more-info') ?>
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="info">
		<div class="container">
			<div class="row">
				<div class="col-md-6 subtitle">
					Nueva convocatoria		
				</div>
			</div>
		</div>
	</div>
</div>