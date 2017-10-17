<?php

$this->layout('layout', [
    'bodyClass' => 'dashboard',
    'title' => 'Dashboard :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

// We include alert messages in this layout, so it will be processed before the
// main layout. Therefore the main layout won't repeat them
?>

<div class="dashboard">

    <?= $this->supply('dashboard-sections', $this->insert('dashboard/partials/sections')) ?>

    <?= $this->supply('dashboard-messages', $this->insert("partials/header/messages")) ?>

    <?= $this->supply('dashboard-content') ?>

</div>

<?php $this->replace() ?>

<?php $this->section('sidebar-header') ?>

    <?= $this->supply('dashboard-sidebar-header', $this->insert("dashboard/partials/sidebar_header")) ?>

<?php $this->replace() ?>

<?php $this->section('head') ?>
    <link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/dropzone/dist/min/dropzone.min.css" />
    <link rel="stylesheet" href="<?= SRC_URL ?>/assets/vendor/simplemde/dist/simplemde.min.css" type="text/css" />
    <link rel="stylesheet" href="<?= SRC_URL ?>/assets/vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.css" type="text/css" />
    <link href="<?= SRC_URL ?>/assets/css/typeahead.css" rel="stylesheet">
<?php $this->append() ?>

<?php $this->section('footer') ?>

    <?= $this->insert('dashboard/partials/javascript') ?>

<?php $this->append() ?>
