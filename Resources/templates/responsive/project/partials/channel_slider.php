<?php
    $channels = $this->channels;
    $currentNode = $this->get_config('current_node'); 
?>

<div class="row">
    <span class="channel-label col-md-4 col-xs-4">
        <img src="/assets/img/project/channel.svg" width="20" alt=""> <?= $this->text('regular-channel') ?>
    </span>
    <div class="slider-channels col-md-8 col-xs-7">
        <?php foreach ($channels as $channel) : ?>
            <?php if ($channel->id != $currentNode && $channel->active): ?>
                <div>
                    <a href="<?= SRC_URL . "/channel/$channel->id" ?>"
                    class="btn"
                    style="<?= $channel->owner_background ? 'background-color: '.$channel->owner_background :  '' ?>"
                    title="<?= $channel->name ?>"
                    >
                        <?= $channel->name ?>
                    </a>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
