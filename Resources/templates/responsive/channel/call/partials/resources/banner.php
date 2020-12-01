<?php $channel=$this->channel; ?>

<div class="section banner-header">
	<div class="custom-header">
		<div class="pull-left">
			<a href="<?= '/channel/'.$this->channel->id ?> ">
				<img src="<?= $channel->logo ? $channel->logo->getlink(0,40) : '' ?>" height="40px">
			</a>
		</div>
		<div class="pull-right">
			<span><?= $this->text('call-header-powered-by') ?></span>
          	<a href="<?= $this->get_config('url.main') ?>">
          		<img height="30" src="<?= '/assets/img/goteo-white-green.png' ?>" >
          	</a>
      	</div>
	</div>
	<div class="image">
		<img src="/assets/img/channel/call/resources/goteo-1920x600_RECURSOS.jpg" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
		<img src="/assets/img/channel/call/resources/goteo-1400x600_RECURSOS.jpg" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
		<img src="/assets/img/channel/call/resources/goteo-1051x600_RECURSOS.jpg" class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
		<img src="/assets/img/channel/call/resources/goteo-550x600_RECURSOS.jpg" class="img-responsive visible-xs">
	</div>

	<div class="banner-info terms">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-7 col-sm-8 col-xs-12">
					<div>
						<span class="title">
						<?= $this->text('channel-call-resources-banner-title') ?>
						</span>
					</div>
					<div class="description">
						<?= $this->text('channel-call-resources-banner-description') ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="info terms">
		<div class="container">
			<div class="row">
				<div class="col-md-10 item item-resources">
					<h5 class="title">
						<?= $this->text('channel-call-resources-filter-title') ?>
					</h5>	
				</div>
			</div>
		</div>
	</div>
</div>