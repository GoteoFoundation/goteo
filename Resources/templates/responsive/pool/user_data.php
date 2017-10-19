<?php

$this->layout('pool/layout');

$this->section('dashboard-content-pool');

?>



    <h2 class="col-sm-offset-1 padding-bottom-2"><?= $this->text('pool-make-sure-title') ?></h2>

    <?= $this->insert('pool/partials/invest_header_form') ?>

    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

    <form class="form col-sm-offset-1" id="make-sure-form" role="form" method="POST" action="/pool/<?= $this->invest->id ?>">

        <?= $this->supply('invest-form', $this->insert('invest/partials/invest_address_form')) ?>

        <?= $this->insert('invest/partials/invest_submit_form') ?>

	</form>


<?php $this->replace() ?>
