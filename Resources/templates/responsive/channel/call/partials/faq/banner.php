<?php //Extract the first title for the element embebed in the header banner ?>
<?php $questions=$this->questions; ?>

<?php $first_faq=current($questions); ?>


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
		<?php $header_image=$this->faq->getBannerHeaderImage(); ?>
		<?php $header_image_md=$this->faq->getBannerHeaderImageMd(); ?>
		<?php $header_image_sm=$this->faq->getBannerHeaderImageSm(); ?>
		<?php $header_image_xs=$this->faq->getBannerHeaderImageXs(); ?>
		<img src="<?= $header_image->getLink(1920, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
		<img src="<?= $header_image_md->getLink(1400, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
		<img src="<?= $header_image_sm->getLink(1051, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
		<img src="<?= $header_image_xs->getLink(550, 600, true) ?>" class="img-responsive visible-xs">
	</div>

	<div class="banner-info terms">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-7 col-sm-9 col-xs-8">
					<div>
						<span class="title">
						<?= $this->faq->banner_title ?>
						</span>
					</div>
					<div class="description">
						<?= $this->faq->banner_description ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="info terms">
		<div class="container">
			<div class="row">
				<div class="col-md-10 item">
					<h2 class="title" <?= $this->faq->question_color ? 'style="color:'.$this->faq->question_color.';"' : '' ?> role="button" data-toggle="collapse" href="<?= '#collapse-'.$first_faq->id ?>" aria-expanded="true">
						<?= $first_faq->title ?>
					</h2>	
				</div>
			</div>
		</div>
	</div>
</div>