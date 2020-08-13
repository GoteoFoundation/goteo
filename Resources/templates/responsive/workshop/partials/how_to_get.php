<div class="how-to-get">
	<div class="container">
		<div class="row">
			<div class="col-md-3 description">
				<h2>
					<?= $this->text('workshop-where-label') ?>
				</h2>
				<strong><?= $this->workshop->venue ?></strong>
				<div class="venue-address">
					<?= $this->workshop->venue_address ?>
				</div>
				<h2>
					<?= $this->text('workshop-how-label') ?>
				</h2>
				<?= $this->markdown($this->workshop->how_to_get) ?>
			</div>
			<div class="col-md-9">
				<?= $this->workshop->map_iframe ?>
			</div>	
		</div>
	</div>
</div>