<div class="section terms-items">
	<div class="container">
		<?php foreach($this->questions as $key => $item): ?>
			<?php if($key==0): ?>
			<div class="row">
				<div class="col-md-10">
					<div class="description collapse in" id="<?= 'collapse-'.$item->id ?>" style="<?= $this->colors['secondary'] ? "color:".$this->colors['secondary'].";" : '' ?> ">
					<?= $this->markdown($item->description) ?>
					</div>
				</div>
			</div>

			<?php else: ?>
			<div class="row">
				<div class="col-md-10">
					<h2 id="<?= "faq-{$item->id}" ?>" class="title" <?= $this->faq->question_color ? 'style="color:'.$this->faq->question_color.';"' : '' ?> role="button" data-toggle="collapse" href="<?= '#collapse-'.$item->id ?>" aria-expanded="false">
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

