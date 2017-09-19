<form class="form-horizontal" role="form" method="GET" action="/pool/payment">

    <div class="pool-box">
        <h4><?= $this->text('pool-recharge-amount-text') ?></h4>
        <label for="reward-pool">

            <strong><?= $this->get_currency('html') ?></strong>

            <input id="reward-pool" type="number" min="0" class="form-control input-amount" name="amount" value="<?= $this->amount ? $this->amount : '0' ?>" id="amount" required>

            <button type="submit" class="btn btn-lg btn-cyan"><?= $this->text('recharge-button') ?></button>

        </label>
      </div>
</form>
