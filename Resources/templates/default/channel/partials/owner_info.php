<?php
$channel=$this->channel;
?>
<div class="owner-info rounded-corners" <?php if($channel->owner_background) echo 'style="background-color:'.$channel->owner_background.'"'; ?>>
    <div class="avatar">
        <a href="/channel/<?= $this->channel->id ?>">
            <img src="/image/<?= $channel->logo->name ?>" alt="<?= $channel->name ?>"/><br />
        </a>
    </div>
    <?php $this->section('channel-owner-info') ?>
    <div class="info" <?php if($channel->owner_font_color) echo 'style="color:'.$channel->owner_font_color.'"'; ?>>

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
    </div>
    <?php $this->stop() ?>
    <!-- enlaces sociales  -->
    <ul class="social">
           <?php if ($channel->facebook): ?>
           <li class="facebook"><a <?= $channel->owner_social_color=='grey' ? 'class="grey"' : '' ?>  href="<?= $channel->facebook ?>" target="_blank">F</a></li>
            <?php endif ?>
            <?php if ($channel->google): ?>
            <li class="grey"><a <?= $channel->owner_social_color=='grey' ? 'class="grey"' : '' ?> href="<?= $channel->google ?>" target="_blank">G</a></li>
            <?php endif ?>
               <?php if ($channel->twitter): ?>
            <li class="twitter"><a <?= $channel->owner_social_color=='grey' ? 'class="grey"' : '' ?> href="<?= $channel->twitter ?>" target="_blank">T</a></li>
            <?php endif ?>
            <?php if ($channel->linkedin): ?>
            <li class="linkedin"><a <?= $channel->owner_social_color=='grey' ? 'class="grey"' : '' ?> href="<?= $channel->linkedin ?>" target="_blank">L</a></li>
            <?php endif ?>
    </ul>
</div>
