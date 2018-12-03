<?php
$this->layout('pool/layout');

$this->section('dashboard-content-pool');

$type=$this->type;

?>

<div class="pool-container">
    <h2><?= $type=='pool' ? $this->text('pool-recharge-title') : $this->text($type.'-select-amount-title') ?></h2>

    <?php if($type=='pool'): ?>

      <div class="pool-conditions clear-both">
      	<p><?= $this->text('dashboard-my-wallet-pool-info') ?> <a data-toggle="modal" data-target="#poolModal" href=""><?= $this->text('regular-here') ?></a></p>
      </div>

    <?php elseif($this->pool): ?>

    <div class="wallet-available">
      <?= $this->text('dashboard-my-wallet-available', amount_format($this->pool->amount)) ?>
    </div>

    <?php endif; ?>

    <?= $this->insert('pool/partials/amount_box', [
      'description' => $type=='pool' ? $this->text('pool-recharge-amount-text') : $this->text($type.'-select-amount-description'),
      'button_text'  => $type=='pool' ? $this->text('recharge-button') : $this->text('landing-donor-button'),
      'form_action' => $type=='pool' ? '/pool/payment' : '/donate/payment',
      'amount'  => $this->amount
    ]) ?>

</div>

<!-- Modal -->
<div class="modal fade" id="poolModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $this->text('invest-modal-pool-title') ?></h4>
      </div>
      <div class="modal-body">
        <?= $this->text('dashboard-my-wallet-modal-pool-info') ?>
      </div>
    </div>
  </div>
</div>


<?php $this->replace() ?>
