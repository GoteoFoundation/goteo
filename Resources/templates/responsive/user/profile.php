<?php

$this->layout('layout', [
    'bodyClass' => 'user',
    'title' =>  ucfirst($this->user->name). ':: Goteo.org' ,
    'meta_description' => $this->text($this->user->about),
    'tw_image' =>  ''
    ]);

$this->section('head');

?>

<link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick-theme.css"/>

<?php

$this->append();

$this->section('content');

$user = $this->user;

$worthcracy = $this->worthcracy;

?>

<div class="user">
	<div class="section main-info">
		<div class="container">
			<div class="row">
				<div class="col-md-2 avatar">
					<img src="<?= $user->avatar->getLink(100, 100, true) ?>" class="img-circle" >
				</div>
				<div class="col-md-7 info">
					<div class="user-label">
						<?= $this->text('profile-name-header') ?>
					</div>
					<h1 class="name">
						<?= ucfirst($user->name) ?>
					</h1>

					<?php if($user->location): ?>

						<div class="location">
							<span class="icon glyphicon glyphicon glyphicon-map-marker" aria-hidden="true"></span>
							<span class="content">
								<?= $user->location ?>
							</span>
						</div>

					<?php endif; ?>
					<p class="description">
						<?= $this->markdown($user->about) ?>
					</p>
					<div class="worthcracy-label">
						<?= $this->text('profile-my_worth-header') ?>
					</div>
					<?php if($worthcracy): ?>
						<?php
							$num_levels=count($worthcracy);
							$level_width=97/$num_levels;
						?>
						<ul class="list-inline worthcracy-chart hidden-xs">
						<?php foreach($worthcracy as $level): ?>
							<li style="width:<?= $level_width.'%' ?>">
								<div class="text-center label-amount  ">
									<?= '> '.amount_format($level->amount) ?>
								</div>
								<div class="chart <?= $level->id<=$user->worth ? 'green' : 'grey' ?>">
								</div>
								<div class="text-center label-name <?= $level->id==$user->worth ? 'green' : '' ?>">
									<?= $level->name ?>
								</div>
							</li>
						<?php endforeach; ?>
						</ul>
						<?php if($user->worth): ?>
						<div class="worthcracy-text visible-xs">
							<?= '> '.amount_format($worthcracy[$user->worth]->amount).' / '.$worthcracy[$user->worth]->name ?>
						</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<div class="col-md-3 stats">
					<h2 class="title">
						<?= $this->text('profile-stats-title') ?>
					</h2>
					<ul class="list-unstyled">
						<?php if($this->my_projects): ?>
							<li class="projects">
								<div class="item-label">
									<?= $this->text('profile-stats-projects') ?>
								</div>
								<div class="item-data">
									<?= count($this->my_projects) ?>
								</div>
							</li>
						<?php endif ?>
						<li class="invests">
							<div class="item-label">
								<?= $this->text('profile-stats-invests') ?>
							</div>
							<div class="item-data">
								<?= $user->num_invested ?: 0 ?>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="section extra-info">
		<div class="fluid-container">
			<div class="container">

				<?php if($this->user->keywords): ?>
					<div class="row item">
						<div class="col-xs-3 col-md-2">
							<div class="icon-item">
								<i class="fa fa-search" aria-hidden="true"></i>
							</div>
						</div>
						<div class="col-xs-9 col-md-10">
							<div class="label-item">
								<?= $this->text('profile-keywords-header') ?>
							</div>
							<div>
								<?= ucfirst($this->user->keywords) ?>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if($this->user->webs): ?>
					<div class="row item">
						<div class="col-xs-3 col-md-2">
							<div class="icon-item">
								<i class="fa fa-link" aria-hidden="true"></i>
							</div>
						</div>
						<div class="col-xs-9 col-md-10">
							<div class="label-item">
								<?= $this->text('profile-webs-header') ?>
							</div>
							<ul class="list-unstyled">
							    <?php foreach ($this->user->webs as $link): ?>
							    <li>
							    	<a href="<?= $link->url ?>" target="_blank" rel="nofollow"><?= $link->url ?></a>
							    </li>
							    <?php endforeach ?>
							</ul>
						</div>
					</div>
				<?php endif; ?>

				<?php if($user->twitter || $user->facebook || $user->instagram): ?>

					<div class="row item social-item">
						<div class="col-xs-3 col-md-2">
							<div class="icon-item">
								<i class="fa fa-users" aria-hidden="true"></i>
							</div>
						</div>
						<div class="col-xs-9 col-md-10 social-icon">
							<div class="label-item">
								<?= $this->text('profile-social-header') ?>
							</div>
							<?php if($user->twitter): ?>
								<a href="<?= $user->twitter ?>" target="_blank" rel="nofollow">
									<img src="<?= SRC_URL ?>/assets/img/social/twitter_circle.png" >
								</a>
							<?php endif; ?>

							<?php if($user->facebook): ?>
								<a href="<?= $this->user->facebook ?>" target="_blank" rel="nofollow">
									<img src="<?= SRC_URL ?>/assets/img/social/facebook_circle.png" >
								</a>
							<?php endif; ?>

							<?php if($user->instagram): ?>
								<a href="<?= $this->user->instagram ?>" target="_blank" rel="nofollow">
									<img width="40" src="<?= SRC_URL ?>/assets/img/social/instagram_circle.png" >
								</a>
							<?php endif; ?>

						</div>

					</div>

				<?php if (!$this->stories && ($this->invest_on || $this->my_projects)): ?>

				    <div class="row border-bottom spacer-30"></div>

				<?php endif; ?>

			<?php endif; ?>

			</div>
		</div>
	</div>

	<?php if($this->stories): ?>

	<div class="section stories">
		<div class="container">
		    <?= $this->insert('partials/components/stories_slider', [
		        'stories' => $this->stories
		    ]) ?>
	    </div>
	</div>

	<?php endif; ?>

	<?php if($this->my_projects): ?>

	<div class="section projects" >
		<div class="container <?= $this->invest_on ? 'border-bottom' : '' ?> <?= !$this->stories ? 'no-padding-top-xs' : '' ?>">
			<div class="title">
				<?= $this->text('profile-my_projects-header') ?>
			</div>

			<div class="slider slider-projects">
			    <?php foreach ($this->my_projects as $project) : ?>
					<div class="item widget-slide">
						<?php if ($project->isPermanent()): ?>
							<?= $this->insert('project/widgets/normal_permanent', ['project' => $project]) ?>
						<?php else: ?>
							<?= $this->insert('project/widgets/normal', ['project' => $project]) ?>
						<?php endif; ?>
					</div>
			    <?php endforeach ?>
			</div>
		</div>
	</div>

	<?php endif; ?>

	<?php if($this->invest_on): ?>

	<div class="section projects" >
		<div class="container  <?= !$this->stories&&!$this->my_projects ? 'no-padding-top-xs' : '' ?>">
			<div class="title">
				<?= $this->text('profile-invest_on-header') ?>
			</div>

			<div class="slider slider-projects">
			    <?php foreach ($this->invest_on as $project) : ?>
					<div class="item widget-slide">
						<?php if ($project->isPermanent()): ?>
							<?= $this->insert('project/widgets/normal_permanent', ['project' => $project]) ?>
						<?php else: ?>
							<?= $this->insert('project/widgets/normal', ['project' => $project]) ?>
						<?php endif; ?>
					</div>
			    <?php endforeach ?>
			</div>
		</div>

	</div>

	<?php endif; ?>

</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script src="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick.min.js"></script>

<script>

/*
@licstart  The following is the entire license notice for the
JavaScript code in this page.

Copyright (C) 2010  Goteo Foundation

The JavaScript code in this page is free software: you can
redistribute it and/or modify it under the terms of the GNU
General Public License (GNU GPL) as published by the Free Software
Foundation, either version 3 of the License, or (at your option)
any later version.  The code is distributed WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

As additional permission under GNU GPL version 3 section 7, you
may distribute non-source (e.g., minimized or compacted) forms of
that code without the copy of the GNU GPL normally required by
section 4, provided you include this license notice and a URL
through which recipients can access the Corresponding Source.


@licend  The above is the entire license notice
for the JavaScript code in this page.
*/
//user profile

$('.slider-stories').slick({
    dots: true,
    infinite: true,
    speed: 1000,
    fade: true,
    arrows: true,
    cssEase: 'linear',
    prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
    nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>'
});

$('.slider-projects').slick({
    infinite: true,
	slidesToShow: 3,
	slidesToScroll: 1,
	arrows: true,
	dots: true,
	prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
	nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>',
	responsive: [
		{
		breakpoint: 769,
			settings: {
			slidesToShow: 2,
			arrows:false
			}
	},
		{
		breakpoint: 500,
			settings: {
			slidesToShow: 1.2,
			arrows: false,
			infinite: false
			}
	}]
});

</script>

<?php $this->append() ?>
