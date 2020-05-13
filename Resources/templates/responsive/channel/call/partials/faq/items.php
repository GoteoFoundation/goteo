<?php $faq=$this->channel->getFaq ($this->faq_type->name); ?>

<div class="section terms-items">
	<div class="container">
		<?php foreach($faq as $key => $item): ?>
			<?php if($key==0): ?>
			<div class="row">
				<div class="col-md-10">
					<div class="description collapse in" id="<?= 'collapse-'.$item->id ?>">
					<?= $this->markdown($item->description) ?>
					</div>
				</div>
			</div>

			<?php else: ?>
			<div class="row">
				<div class="col-md-10">
					<h2 class="title" role="button" data-toggle="collapse" href="<?= '#collapse-'.$item->id ?>" aria-expanded="false">
							<span class="icon icon-<?= $item->icon ?> icon-3x"></span>
							<?= $item->title ?>
					</h2>

					<div class="description collapse" id="<?= 'collapse-'.$item->id ?>">
						<?= $this->markdown($item->description) ?>
					</div>
				</div>
			</div>

			<?php endif; ?>

		<?php endforeach; ?>

	</div>
</div>

