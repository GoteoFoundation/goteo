<?php $this->layout('home/layout') ?>

<?php $this->section('home-content') ?>

<!-- Banner section -->

<?= $this->insert('home/partials/main_slider') ?>

<?= $this->insert('home/partials/search') ?>

<?= $this->insert('home/partials/call_to_action') ?>

<?= $this->insert('home/partials/adventages') ?>

<?= $this->insert('home/partials/foundation') ?>

<?= $this->insert('home/partials/tools') ?>


<?php $this->replace() ?>