<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h2><?= $this->text('dashboard-menu-projects-rewards') ?></h2>


    <h4><?= $this->project->inCampaign() ? $this->text('dashboard-rewards-notice') : $this->text('dashboard-rewards-investors_table', ['%URL%' => '/api/projects/' . $this->project->id . '/rewards/csv']) ?></h4>

    <?php if($this->invests): ?>
    <table class="footable table">
      <thead>
        <tr>
          <th data-type="number" data-breakpoints="xs">#</th>
          <th data-type="date" data-breakpoints="xs"><?= $this->text('regular-date') ?></th>
          <th data-type="html" data-breakpoints="xs"><?= $this->text('admin-user') ?></th>
          <th><?= $this->text('invest-amount') ?></th>
          <th><?= $this->text('rewards-field-individual_reward-reward') ?></th>
          <th><?= $this->text('regular-actions') ?></th>
        </tr>
      </thead>
      <tbody>
    <?php
        foreach($this->invests as $invest):
            $reward = '<span class="label label-danger">' . $this->text('regular-unknown') . '</span>';
            if($invest->getRewards()) $reward = $invest->getRewards()[0]->reward;
            elseif($invest->resign) $reward = '<span class="label label-info">'.$this->text('dashboard-rewards-resigns').'</span>';
            elseif($invest->call) $reward = '<span class="label label-lilac">'.$this->text('regular-matchfunding').'</span>';
     ?>
        <tr>
          <td><?= $invest->id ?></td>
          <td><?= date_formater($invest->invested) ?></td>
          <td><img src="<?= $invest->getUser()->avatar->getLink(30, 30, true) ?>" alt="<?= $invest->getUser()->name ?>" class="img-circle"> <?= $invest->getUser()->name ?></td>
          <td><?= amount_format($invest->amount) ?></td>
          <td><?= $reward ?></td>
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
