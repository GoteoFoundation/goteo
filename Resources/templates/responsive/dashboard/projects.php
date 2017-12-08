<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">

  <div class="inner-container">

    <h2>
        <?= $this->text('profile-my_projects-header') ?>
        <a class="pull-right btn btn-lg btn-fashion" href="/project/create"><?= $this->text('regular-create') ?></a>
    </h2>

    <div class="projects-container" id="my-projects-container">
        <?= $this->insert('dashboard/partials/projects_interests', [
            'projects' => $this->projects,
            'total' => $this->projects_total,
            'interests' => null,
            'auto_update' => '/dashboard/ajax/projects/mine',
            'limit' => $this->limit,
            'admin' => true
            ]) ?>
    </div>

  </div>
</div>

<?php $this->replace() ?>
