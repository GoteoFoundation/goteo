<div class="section post-content">
	<div class="container">
		<div class="text">
			<?php if($this->post->image): ?>
				<p class="text-center">
					<img src="<?= $this->post->image->getLink(800, 0) ?>" class="img-responsive main-image">
				</p>
			<?php endif; ?>
			<?= $this->post->text ?>
		</div>
	</div>
</div>
</div>