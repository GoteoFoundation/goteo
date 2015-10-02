<?php $this->section('header') ?>
    <?= $this->insert("partials/header") ?>
<?php $this->stop() ?>

<?php $this->section('sub-header') ?>
<?php $this->stop() ?>

<?= $this->supply('messages', $this->insert("partials/header/messages")) ?>

<?php $this->section('content') ?>
<?php $this->stop() ?>
