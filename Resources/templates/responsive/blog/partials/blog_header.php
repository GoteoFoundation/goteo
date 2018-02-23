<?php $author=$this->post->getAuthor(); ?>

<div class="section post-header">
	<div class="image">
		<img src="https://data.goteo.org/1920x600c/06-conjuntament.jpg" width="1920" height="600" class="display-none-important img-responsive  hidden-xs visible-up-1400">
		<img src="https://data.goteo.org/1920x600c/06-conjuntament.jpg" width="1400" height="500" class="display-none-important img-responsive  hidden-xs visible-1051-1400">
		<img src="https://data.goteo.org/1920x600c/06-conjuntament.jpg" width="1051" height="460" class="display-none-important img-responsive  hidden-xs visible-768-1050">
		<img src="https://data.goteo.org/1920x600c/06-conjuntament.jpg" width="750" height="600" class="img-responsive visible-xs">
	</div>
	<div class="info">
		<div class="container">
			<h1>
				<?= $this->post->title ?>
			</h1>
			<div class="subtitle">
				Las instituciones ganan visiblidad y reconocimiento asociados a proyectos para el bien común e iniciativas respaldadas por la sociedad Lorem ipsum dolor sit amet.
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
				</li>
			</div>
		</div>
	</div>
</div>

<div class="section post-content">
	<div class="container">
		<div class="text">
			<?= $this->markdown($this->post->text) ?>
		</div>
	</div>
</div>