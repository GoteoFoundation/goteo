<?php

$this->layout('pool/layout');

$this->section('dashboard-content-pool');

?>

<div class="pool-container">

    <h2 class="padding-bottom-2"><?= $this->text('pool-make-sure-title') ?></h2>

    <?= $this->insert('pool/partials/invest_header_form') ?>

    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

    <h3 class="clear-both padding-bottom-2 clear-both"><?= $this->text('invest-address-title') ?></h3>

    <?= $this->form_form($this->raw('form')); ?>

</div>

<?php $this->replace() ?>
