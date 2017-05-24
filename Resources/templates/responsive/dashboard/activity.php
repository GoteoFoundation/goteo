<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="container general-dashboard">
    <?= $this->raw('message') ?>
</div>

<div class="container general-dashboard projects-container" id="my-projects-container">
    <h2><?= $this->text('profile-my_projects-header') ?></h2>
    <?= $this->insert('dashboard/partials/projects_interests.php', [
        'projects' => $this->projects,
        'total' => $this->projects_total,
        'interests' => null,
        'auto_update' => '/dashboard/ajax/projects/mine',
        'limit' => $this->limit
        ]) ?>
</div>

<div class="container general-dashboard projects-container" id="projects-interests-container">
    <h2><?= $this->text('profile-suggest-projects-interest') ?></h2>
    <?= $this->insert('dashboard/partials/projects_interests.php', [
        'projects' => $this->favourite,
        'total' => $this->favourite_total,
        'interests' => $this->interests,
        'auto_update' => '/dashboard/ajax/projects/interests',
        'limit' => $this->limit
        ]) ?>
</div>

<div class="container general-dashboard projects-container" id="projects-support-container">
    <h2><?= $this->text('profile-invest_on-header') ?></h2>
    <?= $this->insert('dashboard/partials/projects_interests.php', [
        'projects' => $this->invested,
        'total' => $this->invested_total,
        'interests' => null,
        'auto_update' => '/dashboard/ajax/projects/invested',
        'limit' => $this->limit
        ]) ?>
</div>

<?php $this->replace() ?>
