<?php

$this->layout('admin/container');

$this->section('admin-container-head');

?>
    <h2><?= $this->text('admin-stats') ?></h2>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript" src="<?= SRC_URL ?>/assets/js/admin/stats-invests.js"></script>

<?php $this->append() ?>
