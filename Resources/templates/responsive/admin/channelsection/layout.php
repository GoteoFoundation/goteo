<?php

$this->layout('admin/container');

$this->section('admin-container-head');

?>
    <h2><?= $this->text('translator-node_sections') ?></h2>

    <?= $this->insert('admin/partials/search_box') ?>

    <?= $this->supply('admin-channelsection-head') ?>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<?php $this->append() ?>
