<div class="section intro" >
	<div class="container" id="rol-container">
		<div class="row" id="pitcher">
			<div class="col-md-6 left-side">
				<img class="img-responsive" src="/assets/img/channel/call/owner.png">
			</div>
			<div class="col-md-6 right-side">
				<h2 class="title"><?= $this->text('channel-call-intro-pitcher-tab-title') ?></h2>
				<p class="description">
					<?= $this->text('channel-call-intro-pitcher-tab-description') ?>
				</p>
				<a href="<?= $this->text('channel-call-intro-pitcher-tab-action-url') ?>" target="_blank" class="btn btn-transparent">
					<?= $this->text('channel-call-intro-pitcher-tab-action') ?>
				</a>
				<img data-toggle="modal" data-target="#PitcherVideoModal" class="cover" src="/assets/img/channel/call/video_covers/pitch.png">
			</div>
		</div>
		<div class="row" style="display:none" id="matcher">
			<div class="col-md-6 left-side">
				<img class="img-responsive" src="/assets/img/channel/call/matcher.png">
			</div>
			<div class="col-md-6 right-side">
				<h2 class="title"><?= $this->text('channel-call-intro-matcher-tab-title') ?></h2>
				<p class="description">
					<?= $this->text('channel-call-intro-matcher-tab-description') ?>
				</p>
				<a href="<?= $this->text('channel-call-intro-matcher-tab-action-url') ?>" target="_blank" class="btn btn-transparent">
					<?= $this->text('channel-call-intro-matcher-tab-action') ?>
				</a>
				<img data-toggle="modal" data-target="#MatcherVideoModal" class="cover" src="/assets/img/channel/call/video_covers/matcher.png">
			</div>
		</div>
		<div class="row" style="display:none" id="donor">
			<div class="col-md-6 left-side" >
				<img class="img-responsive" src="/assets/img/channel/call/donor.png">
			</div>
			<div class="col-md-6 right-side">
				<h2 class="title"><?= $this->text('channel-call-intro-donor-tab-title') ?></h2>
				<p class="description">
					<?= $this->text('channel-call-intro-donor-tab-description') ?>
				</p>
				<a href="<?= $this->text('channel-call-intro-donor-tab-action-url') ?>" target="_blank" class="btn btn-transparent">
				<?= $this->text('channel-call-intro-donor-tab-action') ?>
				</a>
				<img data-toggle="modal" data-target="#DonorVideoModal" class="cover" src="/assets/img/channel/call/video_covers/donor.png">
			</div>
		</div>
		<div class="row" style="display:none" id="goteo">
			<div class="col-md-6 left-side">
				<img class="img-responsive" src="/assets/img/channel/call/goteo.png">
			</div>
			<div class="col-md-6 right-side">
				<h2 class="title"><?= $this->text('channel-call-intro-factory-tab-title') ?></h2>
				<p class="description">
					<?= $this->text('channel-call-intro-factory-tab-description') ?>
				</p>
				<a href="<?= $this->text('channel-call-intro-factory-tab-action-url') ?>" target="_blank" class="btn btn-transparent">
					<?= $this->text('channel-call-intro-factory-tab-action') ?>
				</a>
				<img data-toggle="modal" data-target="#GoteoVideoModal" class="cover" src="/assets/img/channel/call/video_covers/goteo.png">
			</div>
		</div>
	</div>
</div>