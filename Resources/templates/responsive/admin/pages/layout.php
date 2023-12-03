<?php

$this->layout('admin/container');

$this->section('admin-container-head');

?>
    <h2><?= $this->text('admin-pages') ?></h2>

    <?= $this->insert('admin/partials/search_box') ?>

<?php $this->replace() ?>
