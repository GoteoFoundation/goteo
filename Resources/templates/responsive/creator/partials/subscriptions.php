<?php
$subscriptions = $this->subscriptions;
if (empty($subscriptions))
    return;
?>

<section class="subscriptions">
    <h2><?= $this->t('regular-subscriptions') ?></h2>

    <div class="subscription-grid">
        <?php foreach($subscriptions as $subscription): ?>
            <?= $this->insert('creator/partials/subscription_item', ['subscription' => $subscription]); ?>
        <?php endforeach; ?>
    </div>
</section>
