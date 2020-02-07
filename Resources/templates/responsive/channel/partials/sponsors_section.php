<?php $channel=$this->channel; ?>

<?php if($channel->getSponsors()): ?>

<div class="section sponsors-section">
	<div class="container">
		<h2 class="title text-center"><?= $this->text('node-header-sponsorby') ?></h2>
		<ul class="list-inline text-center">
	    <?php foreach ($channel->getSponsors() as $sponsor): ?>
		<?php $sponsor_image=$sponsor->getImage(); ?>
			<li>
				<img src="<?= $sponsor_image->getLink(200, 0, false) ?>" >
			</li>
	<?php endforeach; ?>
		</ul>
    </div>
</div>

<?php endif; ?>
