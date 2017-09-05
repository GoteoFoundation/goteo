<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h2><?= $this->text('dashboard-menu-projects-rewards') ?></h2>

    <?php if($this->invests): ?>
    <table class="footable table">
      <thead>
        <tr>
          <th data-type="number" data-breakpoints="xs">#</th>
          <th data-type="date" data-breakpoints="xs"><?= $this->text('regular-date') ?></th>
          <th data-type="html" data-breakpoints="xs"><?= $this->text('admin-user') ?></th>
          <th><?= $this->text('invest-amount') ?></th>
          <th><?= $this->text('regular-actions') ?></th>
        </tr>
      </thead>
      <tbody>
    <?php
        foreach($this->invests as $invest):
     ?>
        <tr>
          <td><?= $invest->id ?></td>
          <td><?= date_formater($invest->invested) ?></td>
          <td><img src="<?= $invest->getUser()->avatar->getLink(60, 60, true) ?>" alt="<?= $invest->getUser()->name ?>" class="img-circle"></td>
          <td><?= amount_format($invest->amount) ?></td>
          <td>
            ...
          </td>
        </tr>
    <?php endforeach ?>
      </tbody>
    </table>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

    <?php else: ?>
        <p class="alert alert-danger"><?= $this->text('dashboard-chart-no-invested') ?></p>
    <?php endif ?>
  </div>
</div>

<?php $this->replace() ?>
