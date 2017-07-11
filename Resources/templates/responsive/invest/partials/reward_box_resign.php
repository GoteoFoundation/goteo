    <form class="form-horizontal" role="form" method="GET" action="/invest/<?= $this->project->id ?>/payment">

    <div class="row padding-sm no-padding col-sm-10 col-sm-offset-1">
        <label class="label-reward <?= $this->reward ? '' : 'reward-choosen' ?>" for="reward-empty">
            <div class="col-sm-11 no-padding">
                <input name="reward" class="reward" id="reward-empty" <?= $this->reward ? '' : 'checked="checked"' ?> value="0" type="radio">
                <strong class="margin-left-2"><?= $this->text('invest-resign') ?></strong>
            </div>

            <div class="amount-box">
                <div class="row col-sm-10 col-sm-offset-2 margin-2" id="amount-container">
                    <div class="col-sm-1 col-sm-offset-1 no-padding col-xs-1">
                        <strong class="reward-amount"><?= $this->get_currency('html') ?></strong>
                    </div>
                    <div class="no-padding container-input-amount col-md-4 col-sm-3 col-xs-10">
                        <input type="number" min="0" class="form-control input-amount" name="amount" value="<?= $this->amount ? $this->amount : '0' ?>" id="amount" required>
                    </div>
                    <div class="col-md-5 col-sm-4 col-md-offset-1 reward-button">
                        <button type="submit" class="btn btn-block btn-success col-xs-3 margin-2"><?= $this->text('invest-button') ?></button>
                    </div>
                </div>
            </div>
        </label>
    </div>
    </form>
