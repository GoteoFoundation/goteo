<?php

$this->layout('admin/container');

$this->section('admin-container-head');

?>
    <h2><?= $this->text('admin-users') ?></h2>

    <?= $this->insert('admin/partials/search_box') ?>

    <?= $this->supply('admin-users-head') ?>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript" src="<?= $this->asset('js/admin/users.js') ?>"></script>

<?php $this->append() ?>
