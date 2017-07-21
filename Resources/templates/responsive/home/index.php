<?php $this->layout('home/layout') ?>

<?php $this->section('content') ?>

<!-- Banner section -->

<div class="section main-slider">

	<nav>
		<ul class="list-inline navbar-right hidden-xs">
			<li>
				<a href="/about">
					ABOUT
				</a>
			</li>
			<li>
				<a href="/discover/calls" >
				MATCHFUNDING
				</a>
			</li>
			<li>
				<a href="/project/create" class="btn btn-fashion">CREA UN PROYECTO</a>
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
						<div class="main-info">
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
										<a href="" class="btn btn-white">MÁS INFORMACIÓN</a>
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

<div class="section search" >
	<div class="container">
		<form>
			<div class="row" >
				<div class="col-xs-12" >
					<input type="text" name="keyword" >
					<input type="text" name="location" >
					<button type="submit" class="btn btn-light-green">BUSCAR</button>
				</div>
			</div>
		</form>
	</div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script>

$(document).ready(function(){

  $('.fade').slick({
  dots: true,
  infinite: true,
  speed: 1500,
  fade: true,
  arrows: true,
  cssEase: 'linear',
	});

});

</script>

<?php $this->append() ?>