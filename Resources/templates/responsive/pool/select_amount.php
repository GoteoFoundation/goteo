<?php
$this->layout('pool/layout');

$this->section('dashboard-content-pool');

?>

<div class="pool-container">
    <h2><?= $this->text('pool-recharge-title') ?></h2>
    <div class="pool-conditions clear-both">
    	<p><?= $this->text('dashboard-my-wallet-pool-info') ?> <a data-toggle="modal" data-target="#poolModal" href=""><?= $this->text('regular-here') ?></a></p>
    </div>

    <?= $this->insert('pool/partials/amount_box') ?>

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
