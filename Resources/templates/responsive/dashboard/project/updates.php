<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

  <div class="dashboard-content">

    <h2><?= $this->text('dashboard-menu-projects-updates') ?></h2>

    <?php if($this->errorMsg): ?>
        <div class="alert alert-info"><?= $this->errorMsg ?></div>
    <?php elseif($this->posts): ?>

    <table class="footable table table-striped">
      <thead>
        <tr>
          <th data-type="number" data-breakpoints="xs">#</th>
          <th data-type="date" data-breakpoints="xs"><?= $this->text('regular-date') ?></th>
          <th><?= $this->text('regular-title') ?></th>
        </tr>
      </thead>
      <tbody>
    <?php
        foreach($this->posts as $post):
     ?>
        <tr>
          <td><?= $post->id ?></td>
          <td><?= date_formater($post->date) ?></td>
          <td><?= $post->title ?></td>
        </tr>
    <?php endforeach ?>
      </tbody>
    </table>

    <?php else: ?>
        <?= $this->text('dashboard-project-blog-empty') ?>
    <?php endif ?>

</div>

<?php $this->replace() ?>
