<?php

$this->layout('admin/container');

$this->section('admin-container-head');

?>
    <h2><?= $this->text('admin-communications') ?></h2>

    <?= $this->insert('admin/partials/search_box') ?>

    <?= $this->supply('admin-communication-head') ?>


<?php $this->replace() ?>


<?php $this->section('footer') ?>

<!-- POST PROCESSING THIS JAVASCRIPT BY GRUNT -->
<!-- build:js assets/js/admin-communication.js -->
<script type="text/javascript" src="<?= $this->asset('js/forms.js') ?>"></script>
<script type="text/javascript" src="<?= $this->asset('js/admin/communication.js') ?>"></script>
<!-- endbuild -->

<?php $this->append() ?>
