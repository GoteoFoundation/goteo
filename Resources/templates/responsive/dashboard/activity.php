<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="container general-dashboard" id="projects-container">
    <h2><?= $this->text('profile-suggest-projects-interest') ?></h2>
    <?= $this->insert('dashboard/partials/projects_suggestion.php', [
        'projects' => $this->favourite,
        'interests' => $this->interests,
        'autoUpdate' => true,
        'showMore' => $this->favourite_total > count($this->favourite)
        ]) ?>
</div>

<?php $this->replace() ?>
