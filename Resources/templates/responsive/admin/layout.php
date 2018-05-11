<?php

if($this->is_pronto()) {
    echo json_encode([
        'title' => 'Goteo.org :: Admin :: ' . $this->module_label,
        'content' => $this->supply('admin-messages', $this->insert("partials/header/messages")) . $this->supply('admin-content')
        ]);
    return;
}

$this->layout('layout', [
    'bodyClass' => 'admin',
    'navClass' => 'yellow',
    'sidebarClass' => 'yellow',
    'title' => 'Goteo.org :: Admin :: ' . $this->module_label,
    'meta_description' => $this->text('meta-description-admin')
    ]);

$this->section('content');

// We include alert messages in this layout, so it will be processed before the
// main layout. Therefore the main layout won't repeat them
?>

<div class="admin" id="admin-content">

    <?= $this->supply('admin-messages', $this->insert("partials/header/messages")) ?>

    <?= $this->supply('admin-content') ?>

    <?= $this->supply('admin-modal', $this->insert("admin/partials/modal")) ?>

</div>

<?php $this->replace() ?>

<?php $this->section('sidebar-header') ?>

    <?= $this->supply('admin-sidebar-header', $this->insert("admin/partials/sidebar_header")) ?>

<?php $this->replace() ?>

<?php $this->section('head') ?>

    <?= $this->insert('admin/partials/styles') ?>

<?php $this->append() ?>

<?php $this->section('footer') ?>

    <?= $this->insert('admin/partials/javascript_editors') ?>
    <?= $this->insert('admin/partials/javascript') ?>

<?php $this->append() ?>
