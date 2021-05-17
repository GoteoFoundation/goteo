<?php $channel=$this->channel; ?>

<div class="section banner-header">
	
	<!-- Navbar header -->
	<?= $this->insert('channel/call/partials/navbar') ?>

	<div class="image">
		<?php $header_image=$this->channel->getBannerHeaderImage(); ?>
		<?php $header_image_md=$this->channel->getBannerHeaderImageMd(); ?>
		<?php $header_image_sm=$this->channel->getBannerHeaderImageSm(); ?>
		<?php $header_image_xs=$this->channel->getBannerHeaderImageXs(); ?>

		<picture>
			<source srcset="<?= $header_image->getLink(1920, 600, true) ?>" media="(min-width:1400px)"> 
			<source srcset="<?= $header_image_md->getLink(1400, 600, true) ?>" media="(min-width:1051px)"> 
			<source srcset="<?= $header_image_sm->getLink(1051, 600, true) ?>" media="(min-width:550px)"> 
			<img src="<?= $header_image_xs->getLink(550, 600, true) ?>">
		</picture>
	</div>

	<div class="banner-info">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-7 col-sm-8 col-xs-12">
					<div>
						<span class="title" style="<?= $this->colors['header'] ? "color:".$this->colors['header'].";" : '' ?> <?= $this->colors['header_shadow'] ? "text-shadow:none" : '' ?>" >
						<?= $this->channel->subtitle ?>
						</span>
					</div>
					<div class="description" style="<?= $this->colors['header'] ? "color:".$this->colors['header'].";" : '' ?> <?= $this->colors['header_shadow'] ? "text-shadow:none" : '' ?>" >
						<?= $this->channel->description ?>
					</div>
					<a href="<?= $this->channel->banner_button_url ?>" class="btn btn-yellow scroller"><i class="icon icon-plus icon-2x">		
						</i><?= $this->text('landing-more-info') ?>
					</a>
				</div>
			</div>
		</div>
	</div>

	<?php if($this->type!='available'&&!$this->channel->getSections('intro')): ?>


	<div class="info">
		<div class="container">
			<div class="row">
				<div class="col-md-6 subtitle" style="<?= $this->colors['secondary'] ? "color:".$this->colors['secondary'] : '' ?>">
					<?= $this->text('channel-call-main-info-subtitle') ?>
				</div>
			</div>
		</div>
	</div>

	<?php elseif($this->channel->getSections('intro')&&$this->type!='available'): ?>

	<div class="info intro">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<ul class="list-inline filters" >
							<li class="selected" data-rol="pitcher">
								<img height="22px" src="/assets/img/channel/call/characters_icons/pitcher.png">
								<?= $this->text('channel-call-intro-pitcher-title') ?>
							</li>
							<li data-rol="matcher">
								<img height="22px" src="/assets/img/channel/call/characters_icons/matcher.png">
								<?= $this->text('channel-call-intro-matcher-title') ?>
							</li>
							<li data-rol="donor">
								<img height="22px" src="/assets/img/channel/call/characters_icons/donor.png">
								<?= $this->text('channel-call-intro-donor-title') ?>
							</li>
							<li data-rol="goteo" style="padding: 10px;">
								<img height="18px" src="/assets/img/channel/call/characters_icons/goteo.png">
								<?= $this->text('channel-call-intro-factory-title') ?>
							</li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<?php endif; ?>

</div>