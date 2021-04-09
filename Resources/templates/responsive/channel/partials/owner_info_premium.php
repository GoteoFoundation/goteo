<?php $channel=$this->channel; ?>

<div class="container-fluid owner-info">
	<div class="row">
		<div class="col-md-6 col-center text-right">
			<a class="img-responsive" href="/channel/<?= $this->channel->id ?>">
        		<img src="<?= $channel->logo ? $channel->logo->getlink(400,0) : '' ?>" alt="<?= $channel->name ?>"/>
    		</a>
		</div>
		<div class="col-md-6 info col-center" <?php if($channel->owner_font_color) echo 'style="color:'.$channel->owner_font_color.'"'; ?>>
			<?php $this->section('channel-owner-info') ?>
			    <!-- Nombre y texto presentación -->
			    <h2 class="channel-name premium" <?php if($channel->owner_font_color) echo 'style="color:'.$channel->owner_font_color.'"'; ?>><?= $channel->name ?></h2>
			    <strong class="channel-subtitle"><?= $channel->subtitle ?></strong>
				<p class="channel-description premium"><?= $channel->description; ?></p>
				<p class="channel-hashtag"><?= $channel->hashtag ?></p>
			    <div>
			    <?php if($channel->location): ?>
				    <p class="channel-location">
				    	<span class="glyphicon glyphicon glyphicon-map-marker" aria-hidden="true"></span> <?= $channel->location; ?>
				    </p>
				<?php endif ?>
				     <ul class="social list-inline">
			           <?php if ($channel->facebook): ?>
			           <li class="facebook">
			           		<a href="//<?= $channel->facebook ?>" target="_blank">
			           			<img src="/assets/img/project/facebook.svg" >
			           		</a>
			           	</li>
			            <?php endif ?>

			            <?php if ($channel->twitter): ?>
			            <li class="twitter">
			            	<a  href="//<?= $channel->twitter ?>" target="_blank">
			            		<img src="/assets/img/project/twitter.svg" >
			            	</a>
			            </li>
			            <?php endif ?>
			    </ul>
			    </div>
			<?php $this->stop() ?>
		</div>
	</div>
</div>

