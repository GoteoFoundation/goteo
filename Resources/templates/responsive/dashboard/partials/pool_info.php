<div class="dashboard-content">
  <div class="inner-container">
    <div class="user-pool">
        <h1><?=  $this->text('dashboard-my-wallet-available', amount_format($this->pool->getAmount())) ?></h2>

        <p><?= $this->text('dashboard-my-wallet-pool-info') ?> <a data-toggle="modal" data-target="#poolModal" href=""><?= $this->text('regular-here') ?></a></p>

      <div class="row">
        <div class="col-xs-6 text-right">
            <a href="/discover"  class="btn btn-lg btn-pink"><?= $this->text('dashboard-my-wallet-contribute-button') ?></a>
        </div>
        <div class="col-xs-6 text-left">
            <a href="/pool"  class="btn btn-lg btn-cyan"><?= $this->text('recharge-button') ?></a>
        </div>
      </div>
    </div>
  </div>
</div>