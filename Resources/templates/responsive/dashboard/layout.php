<?php

$this->layout('layout', [
    'bodyClass' => '',
    'title' => 'Dashboard :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

?>

<div class="dashboard">

  <div class="navbar dashboard-menu navbar-fixed-left">
    <?= $this->supply('dashboard-menu', $this->insert('dashboard/partials/menu')) ?>
  </div>

  <div class="container-fluid dashboard-content">
    <?= $this->supply('dashboard-sections', $this->insert('dashboard/partials/sections')) ?>

    <?= $this->supply('dashboard-content') ?>
  </div>

</div>

<?php $this->replace() ?>

<?php $this->section('head') ?>
    <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/dropzone/dist/min/dropzone.min.css" />
<?php $this->append() ?>

<?php $this->section('footer') ?>

    <script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/Sortable/Sortable.min.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/dropzone/dist/min/dropzone.min.js"></script>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/js/dashboard/ajax-utils.js"></script>

<?php $this->append() ?>
