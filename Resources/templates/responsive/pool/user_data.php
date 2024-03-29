<?php

$this->layout('pool/layout');

$this->section('dashboard-content-pool');

?>

<div class="pool-container">

    <h2 class="padding-bottom-2"><?= $this->text('pool-make-sure-title') ?></h2>

    <?php if ($this->invest->getInvestOrigin()): ?>
        <p>
            <?= $this->t('donate-invest-origin-thanks') ?>
        </p>
    <?php endif; ?>

    <?= $this->insert('pool/partials/invest_header_form') ?>

    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

    <form class="form" id="make-sure-form" role="form" method="POST" action="<?= '/'.$this->type.'/'.$this->invest->id ?>">

        <?= $this->supply('invest-form', $this->insert('invest/partials/invest_address_form')) ?>

        <?= $this->insert('invest/partials/invest_submit_form') ?>

	</form>

</div>

<?php $this->replace() ?>
