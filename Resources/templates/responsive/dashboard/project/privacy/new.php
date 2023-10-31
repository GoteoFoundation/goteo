<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>


<section class="container">
    <h4><?= $this->text('dashboard-project-privacy-add-new-restriction') ?></h4>

    <?= $this->form_form($this->raw('form')) ?>
</section>

<?php $this->replace() ?>
