<div class="section main-slider">

    <?= $this->nav ? $this->insertif($this->nav) : '' ?>

	<?php if($this->banners): ?>
		<div class="slider slider-main">
			<?php foreach($this->banners as $banner): ?>
					<div class="item">
						<div class="image">
							<?php if($banner->image && !$banner->image->is_fallback): ?>

								<picture>
									<source media="(min-width:1400px)" srcset="<?= $banner->image->getLink(1920, 600, true) ?>" class="img-responsive">
									<source media="(min-width:1051px)" srcset="<?= $banner->image->getLink(1400, 500, true) ?>" class="img-responsive">
									<source media="(min-width:750px)" srcset="<?= $banner->image->getLink(1051, 460, true) ?>" class="img-responsive">
									<img src="<?= $banner->image->getLink(750, 600, true) ?>" class="img-responsive">
								</picture>
							<?php else: ?>

								<img loading="lazy" src="/assets/img/blog/header_default.png" width="1920" class="display-none-important header-default img-responsive  hidden-xs visible-up-1400">
								<img loading="lazy" src="/assets/img/blog/header_default_1400x600.png" class="display-none-important header-default img-responsive  hidden-xs visible-1051-1400">
								<img loading="lazy" src="/assets/img/blog/header_default_1051x600.png"  class="display-none-important header-default img-responsive  hidden-xs visible-768-1050">
								<img loading="lazy" src="/assets/img/blog/header_default_750x600.png"  class="img-responsive header-default visible-xs">
								
							<?php endif; ?>

						</div>
						<div class="main-info">
							<div class="container">
								<div class="row">
									<div class="col-lg-6 col-md-7 col-md-offset-1 col-sm-9 col-xs-8">
										<div>
											<span class="title hidden-xs">
											<?= $banner->title ?>
											</span>
										</div>
										<div class="description">
											<?= $banner->description ?>
										</div>
										<a href="<?= $banner->url ?>" class="btn btn-white scroller"><?= $this->button_text ?></a>
									</div>
								</div>
							</div>
						</div>
					</div>
			<?php endforeach; ?>
		</div>

	<?php endif; ?>

</div>
