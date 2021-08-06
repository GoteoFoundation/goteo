<?php $this->layout('impact_discover/layout') ?>

<?php $this->section('impact-discover-content') ?>

<?= $this->insert('impact_discover/partials/filters') ?>

<?= $this->insertIf('impact_discover/partials/mosaic') ?>

<?= $this->insertIf('impact_discover/partials/list_projects') ?>

<?= $this->insertIf('impact_discover/partials/map', ['map' => $this->map]); ?>

<?php $this->replace() ?>
