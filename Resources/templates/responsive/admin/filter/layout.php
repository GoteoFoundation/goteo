<?php

$this->layout('admin/container');

$this->section('admin-container-head');

?>
    <h2><?= $this->text('admin-filters') ?></h2>

    <?= $this->insert('admin/partials/search_box') ?>

    <?= $this->supply('admin-filters-head') ?>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript" src="<?= $this->asset('js/admin/filter.js') ?>"></script>

<?php $this->append() ?>
