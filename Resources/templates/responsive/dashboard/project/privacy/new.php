<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>


<section class="container">
    <h2><?= $this->text('dashboard-project-privacy-add-new-restriction', $this->post->title) ?></h2>

    <?= $this->form_form($this->raw('form')) ?>
</section>

<?php $this->replace() ?>
