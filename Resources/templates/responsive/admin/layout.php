<?php

$this->layout('layout', [
    'bodyClass' => 'admin',
    'title' => 'Admin :: Goteo.org',
    'meta_description' => $this->text('meta-description-admin')
    ]);

$this->section('content');

// We include alert messages in this layout, so it will be processed before the
// main layout. Therefore the main layout won't repeat them
?>

<div class="admin">

    <?= $this->supply('admin-messages', $this->insert("partials/header/messages")) ?>

    <?= $this->supply('admin-content') ?>

</div>

<?php $this->replace() ?>

<?php $this->section('sidebar-header') ?>

    <?= $this->supply('admin-sidebar-header', $this->insert("dashboard/partials/sidebar_header")) ?>

<?php $this->replace() ?>

<?php $this->section('head') ?>

    <?= $this->insert('admin/partials/styles') ?>

<?php $this->append() ?>

<?php $this->section('footer') ?>

    <?= $this->insert('admin/partials/javascript') ?>

<?php $this->append() ?>
