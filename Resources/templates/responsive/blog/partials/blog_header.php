<?php $author=$this->post->getAuthor(); ?>

<div class="section post-header">
	<div class="image">
		<?php if($this->post->image): ?>
			<img src="<?= $this->post->image->getLink(1920, 600, true) ?>" class="display-none-important img-responsive  hidden-xs visible-up-1400">
			<img src="<?= $this->post->image->getLink(1400, 550, true) ?>" class="display-none-important img-responsive  hidden-xs visible-1051-1400">
			<img src="<?= $this->post->image->getLink(1051, 550, true) ?>" class="display-none-important img-responsive  hidden-xs visible-768-1050">
			<img src="<?= $this->post->image->getLink(750, 460, true) ?>" class="img-responsive visible-xs">
		<?php endif; ?>
	</div>
	<div class="info">
		<div class="container">
			<h1>
				<?= $this->post->title ?>
			</h1>
			<div class="subtitle">
				<?= $this->post->subtitle ?>
			</div>
			<ul class="info-extra list-inline">
				<li>
					<img src="<?= $author->avatar->getLink(64, 64, true); ?>" ?>
				</li>
				<li>
					<span class="author">
					<?= $this->text('regular-by').' '?> <strong><?= $author->name ?></strong>
					</span>
					<span class="date">
						<?= date_formater($this->post->date) ?>	
					</span>					
				<li>
				<li class="social hidden-xs">
					<a class="fa fa-twitter" title="" target="_blank" href=""></a>
          			<a class="fa fa-facebook" title="" target="_blank" href=""></a>
          			<a class="fa fa-telegram" title="" target="_blank" href=""></a>
          			<a class="fa fa-whatsapp" title="" target="_blank" href=""></a>
				</li>
			</div>
		</div>
	</div>
</div>