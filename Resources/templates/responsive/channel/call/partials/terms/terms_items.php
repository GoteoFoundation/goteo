<?php $terms=$this->channel->getTerms (); ?>

<div class="section terms-items">
	<div class="container">
		<?php foreach($terms as $key => $term): ?>
			<?php if($key==0): ?>
			<div class="row">
				<div class="col-md-10">
					<div class="description collapse in" id="<?= 'collapse-'.$term->id ?>">
					<?= $this->markdown($term->description) ?>
					</div>
				</div>
			</div>

			<?php else: ?>
			<div class="row">
				<div class="col-md-10">
					<h2 class="title" role="button" data-toggle="collapse" href="<?= '#collapse-'.$term->id ?>" aria-expanded="false">
							<span class="icon icon-<?= $term->icon ?> icon-3x"></span>
							<?= $term->title ?>
					</h2>

					<div class="description collapse" id="<?= 'collapse-'.$term->id ?>">
						<?= $this->markdown($term->description) ?>
					</div>
				</div>
			</div>

			<?php endif; ?>

		<?php endforeach; ?>

	</div>
</div>

