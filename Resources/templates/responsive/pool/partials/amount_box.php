<div class="pool-box">
    <h4><?= $this->description ?></h4>

    <form class="form-inline" role="form" method="GET" action="<?= $this->form_action ?>">

        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon"><?= $this->get_currency('html') ?></div>
                <input id="reward-pool" type="number" min="0" class="form-control input-lg input-amount" name="amount" value="<?= $this->amount ? $this->amount : '0' ?>" id="amount" required>
            </div>
        </div>
        <button type="submit" class="btn btn-lg btn-cyan"><i class="fa fa-download"></i>  <?= $this->button_text ?></button>

    </form>
</div>
