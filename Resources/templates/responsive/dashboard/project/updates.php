<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h2><?= $this->text('dashboard-menu-projects-updates') ?></h2>

    <?php if($this->errorMsg): ?>
        <div class="alert alert-danger"><?= $this->errorMsg ?></div>
    <?php elseif($this->posts): ?>

    <table class="footable table">
      <thead>
        <tr>
          <th data-type="number" data-breakpoints="xs">#</th>
          <th data-type="date" data-breakpoints="xs"><?= $this->text('regular-date') ?></th>
          <th data-type="html"><?= $this->text('blog-published') ?></th>
          <th data-type="html" data-breakpoints="xs"><?= $this->text('regular-image') ?></th>
          <th><?= $this->text('regular-title') ?></th>
          <th><?= $this->text('regular-actions') ?></th>
        </tr>
      </thead>
      <tbody>
    <?php
        foreach($this->posts as $post):
     ?>
        <tr>
          <td><?= $post->id ?></td>
          <td><?= date_formater($post->date) ?></td>
          <td><?= $this->insert('dashboard/project/partials/boolean', ['active' => $post->publish, 'label_type' => 'cyan', 'url' => '/api/projects/' . $post->owner_id . '/updates/' . $post->id . '/publish', 'confirm_yes' => $this->text('publish-confirm-yes'), 'confirm_no' => $this->text('publish-confirm-no') ]) ?></td>
          <td><img src="<?= $post->image ? $post->image->getLink(96, 72, true) : '' ?>" alt="<?= $post->image ?>" /></td>
          <td><?= $post->title ?></td>
          <td>
            <a class="btn btn-default" title="<?= $this->text('regular-view') ?>" href="/project/<?= $this->project->id ?>/updates/<?= $post->id ?>#updates"><i class="icon icon-1x icon-preview"></i></a>
            <a class="btn btn-default" title="<?= $this->text('regular-edit') ?>" href="/dashboard/project/<?= $this->project->id ?>/updates/<?= $post->id ?>"><i class="icon icon-1x icon-edit"></i></a>
          </td>
        </tr>
    <?php endforeach ?>
      </tbody>
    </table>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

    <?php else: ?>
        <p class="alert alert-danger"><?= $this->text('dashboard-project-blog-empty') ?></p>
    <?php endif ?>

    <p>
        <a href="#" class="btn btn-lg btn-cyan"><i class="fa fa-plus"></i> <?= $this->text('form-add-button') ?></a>
    </p>

  </div>
</div>

<?php $this->replace() ?>
