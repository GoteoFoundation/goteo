<?php

$workshop=$this->workshop;

$share_url = $this->get_url() . '/workshop/' . $this->workshop->id;

$share_title = $this->workshop->title;

$facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url);

$twitter_url = 'http://twitter.com/intent/tweet?text=' . urlencode($share_title . ': ' . $share_url . ' #Goteo');


?>
<div class="main-info">
	<div class="container">
		<div class="row" >
			<div class="col-md-3">
				<ul class="sponsors list-inline text-center">
				<?php foreach ($workshop->getSponsors() as $sponsor): ?>
					<?php $sponsor_image=$sponsor->getImage(); ?>
						<li class="sponsor-container">
							<img src="<?= $sponsor_image->getLink(150, 80, false) ?>" >
						</li>
				<?php endforeach; ?>
				</ul>

				<ul class="spheres list-inline text-center">

					<?php foreach ($workshop->getSpheres() as $sphere): ?>
						<li>
			        		<div class="detail-item center-block">
			        			<img class="img-responsive" src="<?= $sphere->getImage()->getLink(40, 40, false) ?>">
			        		</div>
			            	<div class="item-label">
			            		<?= $sphere->name ?>
			            	</div>
	        			</li>
					<?php endforeach; ?>
				</ul>

			</div>
			<div class="col-md-6">
				<?= $this->markdown($this->workshop->description) ?>
			</div>
			<div class="col-md-3">
				<div class="schedule text-center">
					<i class="fa fa-clock-o"></i> <?= $this->workshop->schedule ?>
				</div>

			<?php if($this->workshop->schedule_file_url): ?>
				<div class="file-icon-label">
					<?= $this->text('workshop-file-label') ?>
				</div>
				<div class="file-icon">
					<a target="_blank" href="<?= $this->workshop->schedule_file_url ?>">
						<i class="fa fa-file"> 
						</i>
					</a>
				</div>
			<?php endif; ?>

			<?php if($this->workshop->terms_file_url): ?>
				<div class="file-icon-label terms">
					<?= $this->text('workshop-terms-label') ?>
				</div>
				<div class="file-icon">
					<a target="_blank" href="<?= $this->workshop->terms_file_url ?>">
						<i class="fa fa-file"> 
						</i>
					</a>
				</div>
			<?php endif; ?>

				<div class="share-label">
					<?= $this->text('workshop-share') ?>
				</div>
				<ul class="list-inline share-icons">
					<li>
						<a href="<?= $facebook_url ?>">
	                        <img class="facebook" src="<?= SRC_URL . '/assets/img/project/facebook.png' ?>">
	                    </a>
                    </li>
                    <li>
	                    <a href="<?= $twitter_url ?>">
	                        <img class="twitter" src="<?= SRC_URL . '/assets/img/project/twitter.png' ?>">
	                    </a>
                	</li>
                	<li>
	                    <a href="tg://msg?text=<?= urlencode($share_title).' '.$share_url ?>">
	                        <img class="telegram" src="<?= SRC_URL . '/assets/img/project/telegram.png' ?>">
	                    </a>
                	</li>
				</ul>
			</div>
		</div>
	</div>
</div>