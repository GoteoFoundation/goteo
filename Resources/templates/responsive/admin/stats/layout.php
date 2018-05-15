<?php

$this->layout('admin/container');

$this->section('admin-container-head');

?>
    <h2><?= $this->text('admin-stats') ?></h2>

    <?= $this->supply('admin-stats-head') ?>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript" src="<?= $this->asset('js/admin/stats.js') ?>"></script>

<?php $this->append() ?>
