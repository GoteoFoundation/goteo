<?php $this->layout('dashboard/layout') ?>
<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
    <div class="inner-container">
        <?php if ($this->subscriptions) : ?>
            <h2><?= $this->text('dashboard-rewards-my-subscriptions') ?></h2>
            <table class="table">
                <?php foreach ($this->subscriptions as $subscription) : ?>
                    <?php $product = $subscription['product'] ?>
                    <?php $subscription = $subscription['subscription'] ?>
                    <?php $plan = $subscription['plan'] ?>
                    <?php $price = $subscription['items']['data'][0]['price']; ?>
                    <tr>
                        <td name="id"><?= $product['description'] ?></td>
                        <td><?= \amount_format($price['unit_amount'] / 100) . '/' . $plan['interval'] ?></td>
                        <td><?= \date_formater(date('Y-m-d', $subscription['start_date'])) ?></td>
                        <td><?= \date_formater(date('Y-m-d', $subscription['current_period_end'])) ?></td>
                    </tr>
                <?php endforeach ?>
            </table>

            <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10]) ?>

        <?php else : ?>
            <blockquote><?= $this->text('dashboard-rewards-no-invests') ?></blockquote>
        <?php endif ?>
    </div>
</div>

<?php $this->replace() ?>