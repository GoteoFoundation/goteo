<div class="pool-box">
    <h4><?= $this->description ?></h4>

    <form class="form-inline" role="form" method="GET" action="<?= $this->form_action ?>">

        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon"><?= $this->get_currency('html') ?></div>
                <input id="reward-pool" type="number" min="0" class="form-control input-lg input-amount" name="amount" value="<?= $this->amount ? $this->amount : '0' ?>" id="amount" required>
            </div>
        </div>

        <?php if ($this->has_query('source') || $this->has_query('detail') || $this->has_query('allocated')): ?>
            <input id="source" type="hidden"  name="source" value="<?= $this->get_query('source') ?>">
            <input id="detail" type="hidden"  name="detail" value="<?= $this->get_query('detail') ?>">
            <input id="allocated" type="hidden"  name="allocated" value="<?= $this->get_query('allocated') ?>">
        <?php endif; ?>

        <button type="submit" class="btn btn-lg btn-cyan"><i class="fa fa-download"></i>  <?= $this->button_text ?></button>

    </form>
</div>
