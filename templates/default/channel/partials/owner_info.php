<?php
$channel=$this->channel;
?>
<div class="owner-info rounded-corners" <?php if(!empty($channel->owner_background)) echo 'style="background-color:'.$channel->owner_background.'"'; ?>>
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
            <!--
            <ul>
                <?php $c=0; foreach ($user->webs as $link): ?>
                <li><a href="<?php echo htmlspecialchars($link->url) ?>" target="_blank"><?php echo htmlspecialchars($link->url) ?></a></li>
                <?php $c++; if ($c>=2) break; endforeach ?>
            </ul>
            -->
        </div>
        <!-- enlaces sociales  -->                   
        <ul class="social">
                <?php if (!empty($channel->facebook)): ?>
               <li class="facebook"><a href="<?= htmlspecialchars($channel->facebook) ?>" target="_blank">F</a></li>
                <?php endif ?>
                <?php if (!empty($channel->google)): ?>
                <li class="google"><a href="<?= htmlspecialchars($channel->google) ?>" target="_blank">G</a></li>
                <?php endif ?>
                   <?php if (!empty($channel->twitter)): ?>
                <li class="twitter"><a href="<?= htmlspecialchars($channel->twitter) ?>" target="_blank">T</a></li>
                <?php endif ?>
                <?php if (!empty($channel->linkedin)): ?>
                <li class="linkedin"><a href="<?= htmlspecialchars($channel->linkedin) ?>" target="_blank">L</a></li>
                <?php endif ?>
        </ul>
    </div>