<?php

$this->layout('admin/container');

$this->section('admin-container-head');

?>
    <h2><?= $this->text('admin-stats') ?></h2>

<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

    <?= $this->supply('admin-stats-filters', $this->insert('admin/stats/partials/filters')) ?>

    <?php $this->section('admin-stats-container') ?>
    <?php $this->stop() ?>

<?php $this->replace() ?>
