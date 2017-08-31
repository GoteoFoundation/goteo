<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h2><?= $this->text('dashboard-menu-projects-supports') ?></h2>
    <p><?= $this->text('guide-project-supports') ?></p>

    <?php if($this->errorMsg): ?>
        <div class="alert alert-danger"><?= $this->errorMsg ?></div>
    <?php elseif($this->supports): ?>

    <table class="footable table">
      <thead>
        <tr>
          <th><?= $this->text('regular-title') ?></th>
          <th data-type="html" data-breakpoints="xs"><?= $this->text('regular-description') ?></th>
          <th style="min-width: 120px;"><?= $this->text('regular-actions') ?></th>
        </tr>
      </thead>
      <tbody>
    <?php
    print_r($this->comments);
        foreach($this->supports as $support):
     ?>
        <tr>
          <td><?= $support->support ?></td>
          <td><?= nl2br($support->description) ?></td>
          <td>
            <a class="btn btn-default" title="<?= $this->text('regular-edit') ?>" href="/dashboard/project/<?= $this->project->id ?>/updates/<?= $support->id ?>"><i class="icon icon-1x icon-edit"></i></a>
            <a class="btn btn-default" title="<?= $this->text('regular-view') ?>" href="/project/<?= $this->project->id ?>/updates/<?= $support->id ?>#updates">5 <i class="icon-1x icon icon-partners"></i></a>
          </td>
        </tr>
    <?php endforeach ?>
      </tbody>
    </table>

    <p>
        <a href="#" class="btn btn-lg btn-cyan"><i class="fa fa-plus"></i> <?= $this->text('form-add-button') ?></a>
    </p>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

    <?php else: ?>
        <?= $this->text('dashboard-project-blog-empty') ?>
    <?php endif ?>

  </div>
</div>

<?php $this->replace() ?>
