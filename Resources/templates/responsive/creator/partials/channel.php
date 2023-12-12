<?php
    $channels = $this->channels;
    if (!$channels)
        return;
?>

<section class="channel">
    <h2><?= $this->t('regular-channels') ?></h2>
    <div class="slider slider-channel">
        <?php foreach($this->channels as $channel): ?>
            <article class="channel">
                <a href="/channel/<?= $channel->id ?>">
                    <img width="100" height="100" src="<?= $channel->getLogo()->getLink(100, 0, false) ?>" alt="<?= $channel->name ?>" title="<?= $channel->name ?>">
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
