<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<?= $this->insert('dashboard/partials/pool_info', [
    'pool' => $this->pool
  ]) ?>

<div class="dashboard-content cyan">
  <div class="inner-container">
      <div class="projects-container">
        <h2><?= $this->text('profile-suggest-projects-interest') ?></h2>
        <?= $this->insert('dashboard/partials/projects_interests', [
            'projects' => $this->projects_suggestion,
            'total' => $this->projects_suggestion_total,
            'interests' => $this->interests,
            'auto_update' => '/dashboard/ajax/projects/interests',
            'limit' => $this->limit
            ]) ?>
      </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="poolModal" tabindex="-1" role="dialog" aria-labelledby="poolModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="poolModalLabel"><?= $this->text('invest-modal-pool-title') ?></h4>
      </div>
      <div class="modal-body">
        <?= $this->text('dashboard-my-wallet-modal-pool-info') ?>
      </div>
    </div>
  </div>
</div>

<?php $this->replace() ?>

