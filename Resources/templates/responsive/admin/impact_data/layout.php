<?php

$this->layout('admin/container');

$this->section('admin-container-head');

?>
    <h2><?= $this->text('admin-impact_data') ?></h2>

    <?= $this->insert('admin/partials/search_box') ?>

    <?= $this->supply('admin-filters-head') ?>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<?php $this->append() ?>
