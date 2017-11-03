<?php $channel=$this->channel; ?>

<div class="container-fluid">
	<div class="jumbotron owner-info" <?php if($channel->owner_background) echo 'style="background-color:'.$channel->owner_background.'"'; ?>>
		<div class="row">
			<div class="col-md-3 col-center">
				<a class="img-responsive" href="/channel/<?= $this->channel->id ?>">
            		<img src="<?= $channel->logo->getlink(200,0) ?>" alt="<?= $channel->name ?>"/>
        		</a>
			</div>
			<div class="col-md-9 info col-center" <?php if($channel->owner_font_color) echo 'style="color:'.$channel->owner_font_color.'"'; ?>>
				<?php $this->section('channel-owner-info') ?>
				    <!-- Nombre y texto presentación -->
				    <h2 class="channel-name"><?= $channel->name ?></h2>
				    <p><?= $channel->description; ?></p>
				    <!-- 2 webs -->
				    <?php if ($channel->webs): ?>
					    <ul>
					        <?php $c=0; foreach ($user->webs as $link): ?>
					        <li><a href="<?= $link->url ?>" target="_blank"><?= $link->url ?></a></li>
					        <?php $c++; if ($c>=2) break; endforeach ?>
					    </ul>
				    <?php endif ?>
				<?php $this->stop() ?>
			</div>
		</div>
	    
	    <!-- enlaces sociales  -->
	    <ul class="social list-inline">
	           <?php if ($channel->facebook): ?>
	           <li class="facebook">
	           		<a href="<?= $channel->facebook ?>" target="_blank">
	           			<img src="/assets/img/channel/<?= 'facebook_'.$channel->owner_social_color.'.png' ?>" 
	           		</a></li>
	            <?php endif ?>
	            <?php if ($channel->google): ?>
	            <li class="grey"><a <?= $channel->owner_social_color=='grey' ? 'class="grey"' : '' ?> href="<?= $channel->google ?>" target="_blank">G</a></li>
	            <?php endif ?>
	               <?php if ($channel->twitter): ?>
	            <li class="twitter">
	            	<a  href="<?= $channel->twitter ?>" target="_blank">
	            		<img src="/assets/img/channel/<?= 'twitter_'.$channel->owner_social_color.'.png' ?>" 
	            	</a>
	            </li>
	            <?php endif ?>
	            <?php if ($channel->linkedin): ?>
	            <li class="linkedin"><a <?= $channel->owner_social_color=='grey' ? 'class="grey"' : '' ?> href="<?= $channel->linkedin ?>" target="_blank">L</a></li>
	            <?php endif ?>
	    </ul>
	</div>
</div>

