<?php

  $workshops = $this->workshops;
  $initial_slide = 0;

  foreach ($workshops as $key => $workshop) {
    if ($workshop->date_in >= date('Y-m-d')) {
      $initial_slide = $key;
      break;
    }
  }
?>

<div class="spacer-20 slider slider-workshops" id="slider-workshops" data-initial-slide="<?= $initial_slide ?>">
	<?php foreach($workshops as $workshop): ?>
		<div class="workshop-widget col-md-3">
			<a class="image" href="<?= '/workshop/' . $workshop->id . $this->lang_url_query($this->lang_current())?>">
				<?php if ($workshop->header_image): ?>
					<img loading="lazy" src="<?= $workshop->getHeaderImage()->getLink(265,280, true)?>">
				<?php else: ?>
					<img loading="lazy" src="/assets/img/channel/call/training_vertical.png">
				<?php endif; ?>
				<div class="date"> <?= date('d/m/Y', strtotime($workshop->date_in)) ?> </div>
				<div class="location"> <i class="fa fa-map-marker"></i> <?= ($workshop->workshop_location)? $this->text_truncate($workshop->workshop_location, 30) : ( ($workshop->getLocation()->city)? $this->text_truncate($workshop->getLocation()->city, 30) : $workshop->venue ) ?> </div>
			</a>


			<div class="title">
				<a href="<?= '/workshop/' . $workshop->id . $this->lang_url_query($this->lang_current())?>"><?= $this->text_truncate($workshop->title, 60) ?></a>
			</div>

			<a class="arrow" href="<?= '/workshop/' . $workshop->id . $this->lang_url_query($this->lang_current())?>">
				<span class="icon icon-arrow icon-2x"></span>
			</a>
		</div>
	<?php endforeach; ?>
</div>