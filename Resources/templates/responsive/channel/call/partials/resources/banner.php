<?php $channel=$this->channel; ?>

<div class="section banner-header">
	<!-- Navbar header -->
	<?= $this->insert('channel/call/partials/navbar') ?>
	<div class="image">
		<img src="/assets/img/channel/call/recursos-crowdcoop.jpg" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
		<img src="/assets/img/channel/call/recursos-crowdcoop.jpg" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
		<img src="/assets/img/channel/call/recursos-crowdcoop.jpg" class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
		<img src="/assets/img/channel/call/recursos-crowdcoop.jpg" class="img-responsive visible-xs">
	</div>

	<div class="banner-info terms">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-7 col-sm-8 col-xs-12">
					<div>
						<span class="title" style="<?= $this->colors['header'] ? "color:".$this->colors['header'].";" : '' ?> <?= $this->colors['header_shadow'] ? "text-shadow:none" : '' ?>">
						<?= $this->text('channel-call-resources-banner-title') ?>
						</span>
					</div>
					<div class="description" style="<?= $this->colors['header'] ? "color:".$this->colors['header'].";" : '' ?> <?= $this->colors['header_shadow'] ? "text-shadow:none" : '' ?>">
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