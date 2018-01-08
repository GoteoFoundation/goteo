<div class="section main-slider">

	<nav>
		<ul class="list-inline navbar-right hidden-xs">
			<li>
				<a href="#foundation">
					<?= $this->text('home-menu-our-foundation') ?>
				</a>
			</li>
			<li>
				<a href="#matchfunding" >
					<?= $this->text('home-menu-matchfunding') ?>
				</a>
			</li>
			<li>
				<a href="/project/create" class="btn btn-fashion">
					<?= $this->text('regular-create') ?>		
				</a>
			</li>
			<li>
				<a href="#search" class="search">
					<img src="/assets/img/home/icono_lupa_white.png" >
				</a>
			</li>
		</ul>
	</nav>

	<?php if($this->banners): ?>

		<div class="slider slider-main">
			<?php foreach($this->banners as $banner): ?>
				<?php if($banner->image): ?>
					<div class="item">
						<div class="image">
							<img src="<?= $banner->image->getLink(1920, 600, true) ?>" class="display-none-important img-responsive  hidden-xs visible-up-1400">
		                    <img src="<?= $banner->image->getLink(1400, 500, true) ?>" class="display-none-important img-responsive  hidden-xs visible-1051-1400">
			                <img src="<?= $banner->image->getLink(1051, 460, true) ?>" class="display-none-important img-responsive  hidden-xs visible-768-1050">
		                    <img src="<?= $banner->image->getLink(750, 600, true) ?>" class="img-responsive visible-xs">
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
										<a href="<?= $banner->url ?>" class="btn btn-white"><?= $this->text('invest-more-info') ?></a>
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

<div id="search">
    <button type="button" class="close">×</button>
    <form action="/discover/results">
        <input type="search" name="query" value="" placeholder="escribe tu búsqueda aquí" autocomplete="off"/>
        <div class="tooltip">Entra tu búsqueda y pulsa enter</div>
        <button type="submit" class="btn btn-white">Buscar</button>
    </form>
</div>