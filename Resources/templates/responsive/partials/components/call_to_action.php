<div class="section main-slider">

    <?= $this->nav ? $this->insertif($this->nav) : '' ?>

	<?php if($this->banners): ?>

		<div class="slider slider-main">
			<?php foreach($this->banners as $banner): ?>
				<?php $banner_image=$banner->header_image ? $banner->header_image : $banner->image; ?>
					
				<?php if($banner_image||!$banner->description): ?>
					<div class="item">
						<div class="image">
							<?php if(!$banner->description&&!$banner->header_image): ?>
								<img src="/assets/img/blog/header_default.png" width="1920" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
								<img src="/assets/img/blog/header_default_1400x600.png" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
								<img src="/assets/img/blog/header_default_1051x600.png"  class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
								<img src="/assets/img/blog/header_default_750x600.png"  class="img-responsive header-default visible-xs">

							<?php else: ?>

								<img src="<?= $banner_image->getLink(1920, 600, true) ?>" class="display-none-important img-responsive  hidden-xs visible-up-1400">
			                    <img src="<?= $banner_image->getLink(1400, 500, true) ?>" class="display-none-important img-responsive  hidden-xs visible-1051-1400">
				                <img src="<?= $banner_image->getLink(1051, 460, true) ?>" class="display-none-important img-responsive  hidden-xs visible-768-1050">
			                    <img src="<?= $banner_image->getLink(750, 600, true) ?>" class="img-responsive visible-xs">

							<?php endif; ?>

						</div>
						<div class="main-info">
							<div class="container">
								<div class="row">
									<div class="col-lg-6 col-md-7 col-md-offset-1 col-sm-9 col-xs-8">
										<div>
											<span class="title hidden-xs">
											<?= $banner->description ? $banner->title : $this->text($banner::getSection($banner->section)) ?>
											</span>
										</div>
										<div class="description">
											<?= $banner->description ? $banner->description : $banner->title ?>
										</div>
										<a href="<?= $banner->url ? $banner->url : '/blog/'.$banner->id ?>" class="btn btn-white scroller"><?= $this->button_text ?></a>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>

			<?php endforeach; ?>
		</div>

	<?php endif; ?>

</div>