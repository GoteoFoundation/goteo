<div class="slider slider-workshops" id="slider-workshops">
	<?php foreach($this->workshops as $workshop): ?>
		<div class="workshop-widget col-md-3">
			<a class="image" href="<?= '/workshop/' . $workshop->id ?>">
				<img src="<?= $workshop->getHeaderImage()->getLink(265,280, true)?>">
				<div class="date"> <?= $workshop->date_in ?> </div>
				<div class="location"> <i class="fa fa-map-marker"></i> <?= $workshop->workshop_location ?> </div>
			</a>


			<div class="title">
				<a href="<?= '/workshop/' . $workshop->id ?>"><?= $workshop->title ?></a>
			</div>

			<a class="arrow" href="<?= '/workshop/' . $workshop->id ?>">
				<span class="icon icon-arrow icon-2x"></span>
			</a>
		</div>
	<?php endforeach; ?>
</div>