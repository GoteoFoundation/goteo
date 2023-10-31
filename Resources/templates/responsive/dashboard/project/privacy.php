<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
      <h2><?= $this->text('dashboard-menu-projects-updates-privacy') ?></h2>
      <a href="/dashboard/project/<?= $this->project->id ?>/updates/<?= $this->post->id ?>/privacy/new" class="btn btn-cyan spacer-bottom-20"><i class="fa fa-plus"></i> <?= $this->text('form-add-button') ?></a>

      <?php if($this->rewards): ?>
          <table class="-footable table">
              <thead>
                <tr>
                  <th data-type="number" data-breakpoints="xs">#</th>
                  <th><?= $this->text('regular-title') ?></th>
                  <th style="min-width: 140px"><?= $this->text('regular-actions') ?></th>
                </tr>
              </thead>
              <tbody>
            <?php
                foreach($this->rewards as $reward):
             ?>
                <tr>
                  <td><?= $reward->getReward()->id ?></td>
                  <td><?= $reward->getReward()->reward ?></td>
                  <td>
                    <div class="btn-group">
                        <a class="btn btn-default" title="<?= $this->text('regular-delete') ?>" href="/project/<?= $this->project->id ?>/updates/<?= $this->post->id ?>/delete/<?= $reward->getReward()->id ?>"><i class="fa fa-trash"></i></a>
                    </div>
                  </td>
                </tr>
            <?php endforeach ?>
              </tbody>
            </table>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

    <?php else: ?>
        <blockquote>
            <?= $this->text('dashboard-project-blog-privacy-empty') ?>
        </blockquote>
    <?php endif ?>
  </div>
</div>

<?php $this->replace() ?>
