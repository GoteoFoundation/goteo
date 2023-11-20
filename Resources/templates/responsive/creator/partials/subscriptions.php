<?php
$subscriptions = $this->subscriptions;
?>

<section class="subscriptions">
    <h2 class="text-center text-4xl font-extrabold mt-2">Subscriptions</h2>

    <div>
        <?php foreach($subscriptions as $subscription): ?>
            <?= $this->insert('creator/partials/subscription_item', ['subscription' => $subscription]); ?>
        <?php endforeach; ?>
    </div>
</section>
