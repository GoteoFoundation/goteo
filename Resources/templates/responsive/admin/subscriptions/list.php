<?php
$this->layout('admin/subscriptions/layout');
$subscriptions = $this->subscriptions;
?>

<?php $this->section('admin-container-body'); ?>
        <div class="inner-container">
            <table class="table">
                <thead>
                    <td><?= $this->t('regular-id') ?></td>
                    <td><?= $this->t('regular-user') ?></td>
                    <td><?= $this->t('regular-reward') ?></td>
                    <td><?= $this->t('regular-amount') ?></td>
                    <td><?= $this->t('regular-status') ?></td>
                    <td><?= $this->t('subscription-data-start-date') ?></td>
                    <td><?= $this->t('subscription-data-valid-until') ?></td>
                    <td><?= $this->t('subscription-data-description') ?></td>
                    <td><?= $this->t('regular-actions') ?></td>
                </thead>
                <tbody>
                <?php foreach($this->subscriptions as $stripeSubscription):
                    $data = $stripeSubscription;
                    $user = $data['user'];
                    $reward = $data['reward'];
                    $product = $data['plan']['product'];
                    $plan = $data['plan'];
                    $price = $data['items']['data'][0]['price']['unit_amount'];
                ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= $user->name ?></td>
                        <td><?= $reward->reward ?></td>
                        <td><?= \amount_format($price / 100) . '/' . $plan['interval'] ?></td>
                        <td><span class="label label-info"><?= $data['status'] ?></span></td>
                        <td><?= \date_formater(date('Y-m-d', $data['start_date'])) ?></td>
                        <td><?= \date_formater(date('Y-m-d', $data['current_period_end'])) ?></td>
                        <td><?= $product['description'] ?></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
<?php $this->replace(); ?>
