    <form class="form-horizontal" role="form" method="GET" action="/pool/payment">

    <div class="row no-padding col-sm-10 col-sm-offset-1">
        <label class="label-reward reward-choosen" for="reward-empty">
            <div class="col-sm-11 no-padding">
                <strong class="margin-left-2"><?= $this->text('pool-recharge-amount-text') ?></strong>
            </div>

            <div class="amount-box">
                <div class="row no-padding col-sm-10 col-sm-offset-2 margin-2" id="amount-container">
                    <div class="col-sm-1 col-sm-offset-1 no-padding col-xs-1">
                        <strong class="reward-amount"><?= $this->get_currency('symbol') ?></strong>
                    </div>
                    <div class="no-padding container-input-amount col-md-4 col-sm-3 col-xs-10">
                        <input type="number" min="0" class="form-control input-amount" name="amount" value="<?= $this->amount ? $this->amount : '0' ?>" id="amount" required>
                    </div>
                    <div class="col-md-5 col-sm-4 col-md-offset-1 reward-button">
                        <button type="submit" class="btn btn-block btn-success col-xs-3 margin-2"><?= $this->text('recharge-button') ?></button>
                    </div>
                </div>
            </div>
        </label>
    </div>
    </form>
