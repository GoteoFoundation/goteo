<?php $this->layout('admin/layout') ?>


<?php $this->section('admin-content') ?>

<div class="admin-content">
    <div class="inner-container">
        <h2><?= $this->text('admin-home-title') ?></h2>


        <?= $this->supply('admin-content-analytics', $this->insert('admin/partials/analytics')) ?>

    </div>
</div>

<?php $this->replace() ?>

