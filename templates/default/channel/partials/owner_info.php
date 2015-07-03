<?php
$channel=$this->channel;
?>
<div class="owner-info rounded-corners" <?php if($channel->owner_background) echo 'style="background-color:'.$channel->owner_background.'"'; ?>>
    <div class="avatar">
        <a href="<?= $URL.'/channel/'.$this->channel->id ?>">
            <img src="<?php echo SITE_URL . '/image/' . $channel->logo->name; ?>" alt="<?php echo $channel->name ?>"/><br />
        </a>
    </div>
    <div class="info">
        <!-- Nombre y texto presentación -->
        <h2 class="channel-name"><?= $this->text('regular-channel').' '.$channel->name ?></h2>
        <p><?php echo $channel->description; ?></p>

        <!-- 2 webs -->
        <?php if ($channel->webs): ?>
        <ul>
            <?php $c=0; foreach ($user->webs as $link): ?>
            <li><a href="<?php echo $link->url ?>" target="_blank"><?php echo $link->url ?></a></li>
            <?php $c++; if ($c>=2) break; endforeach ?>
        </ul>
        <?php endif ?>
    </div>
    <!-- enlaces sociales  -->
    <ul class="social">
           <?php if ($channel->facebook): ?>
           <li class="facebook"><a href="<?= $channel->facebook ?>" target="_blank">F</a></li>
            <?php endif ?>
            <?php if ($channel->google): ?>
            <li class="google"><a href="<?= $channel->google ?>" target="_blank">G</a></li>
            <?php endif ?>
               <?php if ($channel->twitter): ?>
            <li class="twitter"><a href="<?= $channel->twitter ?>" target="_blank">T</a></li>
            <?php endif ?>
            <?php if ($channel->linkedin): ?>
            <li class="linkedin"><a href="<?= $channel->linkedin ?>" target="_blank">L</a></li>
            <?php endif ?>
    </ul>
</div>
