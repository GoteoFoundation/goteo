<?php

$this->layout('layout', [
    'bodyClass' => '',
    'title' => 'Dashboard :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

?>

  <?= $this->supply('admin-menu', $this->insert('dashboard/partials/menu')) ?>

  <?= $this->supply('dashboard-content') ?>

<?php $this->replace() ?>

<?php $this->section('head') ?>
    <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/dropzone/dist/min/dropzone.min.css" />
<?php $this->append() ?>

<?php $this->section('footer') ?>

    <script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/Sortable/Sortable.min.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/dropzone/dist/min/dropzone.min.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/js/dashboard/ajax-utils.js"></script>

<?php $this->append() ?>
