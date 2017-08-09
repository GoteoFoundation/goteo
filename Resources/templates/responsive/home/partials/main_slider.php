<div class="section main-slider">

	<nav>
		<ul class="list-inline navbar-right hidden-xs">
			<li>
				<a href="/about">
					<?= $this->text('home-menu-our-foundation') ?>
				</a>
			</li>
			<li>
				<a href="/discover/calls" >
					<?= $this->text('home-menu-matchfunding') ?>
				</a>
			</li>
			<li>
				<a href="/project/create" class="btn btn-fashion">
					<?= $this->text('regular-create') ?>		
				</a>
			</li>
			<li>
				<a href="">
					<img src="/assets/img/home/icono_lupa_white.png" >
				</a>
			</li>
		</ul>
	</nav>

	<?php if($this->banners): ?>

		<div class="slider fade">
			<?php foreach($this->banners as $banner): ?>
				<?php if($banner->image): ?>
					<div>
						<div class="image">
							<img src="<?= $banner->image->getLink(1920, 600, true) ?>" >
						</div>
						<div class="main-info hidden-xs">
							<div class="container">
								<div class="row">
									<div class="col-md-6">
										<div>
											<span class="title">
											<?= $banner->title ?>
											</span>
										</div>
										<div class="description">
											<?= $banner->description ?>
										</div>
										<a href="" class="btn btn-white"><?= $this->text('invest-more-info') ?></a>
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