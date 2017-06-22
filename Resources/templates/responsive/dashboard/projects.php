<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">

    <div class="projects-container" id="my-projects-container">
        <h2><?= $this->text('profile-my_projects-header') ?></h2>
        <?= $this->insert('dashboard/partials/projects_interests', [
            'projects' => $this->projects,
            'total' => $this->projects_total,
            'interests' => null,
            'auto_update' => '/dashboard/ajax/projects/mine',
            'limit' => $this->limit
            ]) ?>
    </div>

</div>

<?php $this->replace() ?>
