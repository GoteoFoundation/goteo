<?php $this->layout('dashboard/layout') ?>
<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
    <div class="inner-container">
        <?php $data = $this->subscription ?>
        <?php $product = $data['product'] ?>
        <?php $subscription = $data['subscription'] ?>
        <?php $plan = $subscription['plan'] ?>
        <?php $price = $subscription['items']['data'][0]['price']; ?>
        <h2><?= $product['description'] ?></h2>
        <table class="table">
            <tr>
                <td><?= $this->t('subscription-data-id') ?></td>
                <td><?= $subscription['id'] ?></td>
            </tr>
            <tr>
                <td><?= $this->t('subscription-data-start-date') ?></td>
                <td><?= \date_formater(date('Y-m-d', $subscription['start_date'])) ?></td>
            </tr>
            <tr>
                <td><?= $this->t('subscription-data-valid-until') ?></td>
                <td><?= \date_formater(date('Y-m-d', $subscription['current_period_end'])) ?></td>
            </tr>
            <tr>
                <td><?= $this->t('subscription-data-description') ?></td>
                <td><?= $product['description'] ?></td>
            </tr>
            <tr>
                <td><?= $this->t('subscription-data-status') ?></td>
                <td><?= $subscription['status'] ?></td>
            </tr>
            <tr>
                <td><?= $this->t('subscription-data-price') ?></td>
                <td><?= \amount_format($price['unit_amount'] / 100) . '/' . $plan['interval'] ?></td>
            </tr>
        </table>
    </div>
</div>

<?php $this->replace() ?>