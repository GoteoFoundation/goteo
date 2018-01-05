<?php $this->layout('discover/layout') ?>

<?php $this->section('discover-content') ?>

<?= $this->insert('discover/partials/main_info') ?>

<?= $this->insert('discover/partials/projects_list') ?>

<?php $this->replace() ?>