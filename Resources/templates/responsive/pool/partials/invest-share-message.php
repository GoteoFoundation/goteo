<?php if($this->type=='pool'): ?>

<h2 class="padding-bottom-2"><?= $this->text('pool-share-title') ?></h2>
<div class="reminder">
  <div class="row spacer">
    <div class="col-sm-6 margin-2">
        <a href="/dashboard/wallet" class="text-decoration-none" >
            <button type="button" class="btn btn-block btn-success" value=""><i class="icon icon-wallet"></i> <?= $this->text('dashboard-menu-pool') ?></button>
        </a>
    </div>
  </div>
</div>

<?php endif; ?>
