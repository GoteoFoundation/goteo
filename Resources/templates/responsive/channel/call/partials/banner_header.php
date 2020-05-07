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
		<?php $header_image_md=$this->channel->getBannerHeaderImageMd(); ?>
		<?php $header_image_sm=$this->channel->getBannerHeaderImageSm(); ?>
		<?php $header_image_xs=$this->channel->getBannerHeaderImageXs(); ?>
		<img src="<?= $header_image->getLink(1920, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
		<img src="<?= $header_image_md->getLink(1400, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
		<img src="<?= $header_image_sm->getLink(1051, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
		<img src="<?= $header_image_xs->getLink(550, 600, true) ?>" class="img-responsive visible-xs">
	</div>

	<div class="banner-info">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-7 col-sm-9 col-xs-8">
					<div>
						<span class="title">
						<?= $this->channel->name ?>
						</span>
					</div>
					<div class="description">
						<?= $this->channel->subtitle ?>
					</div>
					<a href="<?= '/channel/'.$this->channel->id.'/faq' ?>" class="btn btn-yellow scroller"><i class="icon icon-plus icon-2x">		
						</i><?= $this->text('landing-more-info') ?>
					</a>
				</div>
			</div>
		</div>
	</div>

	<?php if($this->type!='available'): ?>

	<div class="info">
		<div class="container">
			<div class="row">
				<div class="col-md-6 subtitle">
					<?= $this->text('channel-call-main-info-subtitle') ?>
				</div>
			</div>
		</div>
	</div>

	<?php endif; ?>

</div>