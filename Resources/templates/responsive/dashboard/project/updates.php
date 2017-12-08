<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h2>
        <?= $this->text('dashboard-menu-projects-updates') ?>
        <a href="/dashboard/project/<?= $this->project->id ?>/updates/new" class="pull-right btn btn-cyan"><i class="fa fa-plus"></i> <?= $this->text('form-add-button') ?></a>
    </h2>

    <div class="auto-hide">
        <div class="inner"><?= $this->text('guide-project-updates') ?></div>
        <div class="more"><i class="fa fa-info-circle"></i> <?= $this->text('regular-help') ?></div>
    </div>

    <?php if($this->posts): ?>

    <table class="-footable table">
      <thead>
        <tr>
          <th data-type="number" data-breakpoints="xs">#</th>
          <th data-type="date" data-breakpoints="xs"><?= $this->text('regular-date') ?></th>
          <th data-type="html"><?= $this->text('blog-published') ?></th>
          <th data-type="html" data-breakpoints="xs"><?= $this->text('regular-image') ?></th>
          <th><?= $this->text('regular-title') ?></th>
          <th style="min-width: 140px"><?= $this->text('regular-actions') ?></th>
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
            <div class="btn-group">
                <a class="btn btn-default" title="<?= $this->text('regular-view') ?>" target="_blank" href="/project/<?= $this->project->id ?>/updates/<?= $post->id ?>#updates"><i class="icon icon-preview"></i></a>
                <a class="btn btn-default" title="<?= $this->text('regular-edit') ?>" href="/dashboard/project/<?= $this->project->id ?>/updates/<?= $post->id ?>"><i class="icon icon-edit"></i></a>
                <?php
                  if($this->languages) {
                    echo $this->insert('dashboard/partials/translate_menu', ['no_title' => true, 'btn_class' => 'btn-default', 'base_link' => '/dashboard/project/' . $this->project->id . '/updates/' . $post->id . '/', 'translated' => $post->getLangsAvailable(), 'percentModel' => $post]);
                  }
                ?>
            </div>
          </td>
        </tr>
    <?php endforeach ?>
      </tbody>
    </table>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

    <?php else: ?>
        <blockquote>
            <?= $this->text('dashboard-project-blog-empty') ?>
        </blockquote>
    <?php endif ?>

    <p>
        <a href="/dashboard/project/<?= $this->project->id ?>/updates/new" class="btn btn-lg btn-cyan"><i class="fa fa-plus"></i> <?= $this->text('form-add-button') ?></a>
    </p>

  </div>
</div>

<?php $this->replace() ?>
