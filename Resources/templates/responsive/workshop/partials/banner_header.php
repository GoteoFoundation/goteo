<div class="section banner-header">
	<div class="image">
		<?php if($this->workshop->header_image): ?>
			<?php $header_image=$this->workshop->getHeaderImage(); ?>
			<img src="<?= $header_image->getLink(1920, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
			<img src="<?= $header_image->getLink(1400, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
			<img src="<?= $header_image->getLink(1051, 600, true) ?>" class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
			<img src="<?= $header_image->getLink(750, 450, true) ?>" class="img-responsive visible-xs">

		<?php else: ?>
			<img src="/assets/img/blog/header_default.png" width="1920" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
			<img src="/assets/img/blog/header_default.png" width="1400" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
			<img src="/assets/img/blog/header_default.png" width="1051" class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
			<img src="/assets/img/blog/header_default.png" width="750"  class="img-responsive header-default visible-xs">
		<?php endif; ?>

	</div>
	<div class="info">
		<div class="container">
			<h1>
				<?= $this->workshop->event_type ? strtok($this->workshop->event_type, '-') : $this->workshop->title ?>
			</h1>
			<div class="subtitle hidden-xs">
				<?= $this->workshop->subtitle ?>
			</div>
			<div class="type-container">
                <span class="type-label" >
                	<?= $this->workshop->city ?>
                </span>
                <span class="type">
                    <?= $this->text('workshop-regular') ?>
                </span>
            </div>
		</div>
	</div>
</div>
