<div class="partner">
	<div class="container">
		<div class="row">
			<?php foreach($this->footer_sponsors as $sponsor): ?>
				<div class="col-xs-12">
					<a target="_blank" href="<?= $sponsor->url ?>" class="center-block">
	            		<img class="img-responsive no-title center-block" src="<?= $sponsor->getImage()->getLink(500, 0, false) ?>" alt="<?= $sponsor->name ?>">
	        		</a>
	        	</div>
	    	<?php endforeach; ?>
		</div>
	</div>
</div>