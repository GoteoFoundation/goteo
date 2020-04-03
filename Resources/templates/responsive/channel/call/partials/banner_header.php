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
		<?php if($this->channel->header_image): ?>
			<img src="<?= $this->post->header_image->getLink(1920, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
			<img src="<?= $this->post->header_image->getLink(1400, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
			<img src="<?= $this->post->header_image->getLink(1051, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
			<img src="<?= $this->post->header_image->getLink(750, 450, true) ?>" class="img-responsive visible-xs">

		<?php else: ?>
			<img src="/assets/img/channel/call/header_default.png" width="1920" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
			<img src="/assets/img/channel/call/header_default.png" width="1400" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
			<img src="/assets/img/channel/call/header_default.png" width="1051" class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
			<img src="/assets/img/channel/call/header_default.png" width="750"  class="img-responsive header-default visible-xs">
		<?php endif; ?>

	</div>

	<div class="banner-info">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-7 col-sm-9 col-xs-8">
					<div>
						<span class="title hidden-xs">
						Cooperativismo en Catalunya
						</span>
					</div>
					<div class="description">
						Espacio digital para la creación y consolidación de proyectos cooperativos
					</div>
						<a href="<?= $banner->url ?>" class="btn btn-yellow scroller"><i class="icon icon-preview icon-2x"></i><?= $this->text('landing-more-info') ?></a>
					</div>
				</div>
			</div>
	</div>

	<div class="info">
		<div class="subtitle">
			Nueva convocatoria		
		</div>
	</div>
</div>