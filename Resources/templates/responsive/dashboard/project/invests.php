<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h2><?= $this->text('dashboard-menu-projects-rewards') ?></h2>


    <p class="exportcsv"><?= $this->project->inCampaign() ? $this->text('dashboard-rewards-notice') : $this->text('dashboard-rewards-investors_table', ['%URL%' => '/api/projects/' . $this->project->id . '/rewards/csv']) ?></p>

    <?php if($this->invests): ?>
    <h5><?= $this->text('dashboard-invests-totals', ['%TOTAL_INVESTS%' => '<strong>' . $this->total_invests . '</strong>', '%TOTAL_USERS%' => '<strong>' . $this->total_users . '</strong>', '%TOTAL_AMOUNT%' => '<strong>' . amount_format($this->total_amount) . '</strong>']) ?></h5>

    <table class="footable table">
      <thead>
        <tr>
          <th data-type="number" data-breakpoints="xs"><?= $this->text('regular-input') ?></th>
          <th data-type="date" data-breakpoints="xs"><?= $this->text('regular-date') ?></th>
          <th data-type="html" data-breakpoints="xs"><?= $this->text('admin-user') ?></th>
          <th><?= $this->text('invest-amount') ?></th>
          <th><?= $this->text('rewards-field-individual_reward-reward') ?></th>
          <th><?= $this->text('admin-address') ?></th>
          <th><?= $this->text('regular-actions') ?></th>
        </tr>
      </thead>
      <tbody>
    <?php
        foreach($this->invests as $invest):
            $resign = $invest->resign;
            $uid = $invest->getUser()->id;
            $name = $invest->getUser()->name;
            $email = $invest->getUser()->email;
            $a = $invest->getAddress();
            $address = $a->address . ', ' . $a->location . ', ' . $a->zipcode .' ' . $a->country;
            $reward = $invest->getRewards() ? $invest->getRewards()[0]->reward : '';
            if($invest->resign) {
                $reward = $address = '';
                if($invest->anonymous) {
                    $uid = $name = $email = '';
                }
                $reward = '<span class="label label-info">'.$this->text('dashboard-rewards-resigns').'</span>';
            }
            if($invest->campaign) {
                $email = $address = $reward = '';
                $resign = true;
                $reward = '<span class="label label-lilac">'.$this->text('regular-matchfunding').'</span>';
            }
            if(!$resign && !$reward) {
                $reward = '<span class="label label-danger">' . $this->text('regular-unknown') . '</span>';
            }


     ?>
        <tr>
          <td><?= $invest->id ?></td>
          <td><?= date_formater($invest->invested) ?></td>
          <td><?php if($uid): ?><img src="<?= $invest->getUser()->avatar->getLink(30, 30, true) ?>" alt="<?= $name ?>" class="img-circle"> <?= $name ?><?php else: ?><?= $this->text('regular-anonymous') ?><?php endif ?> </td>
          <td><?= amount_format($invest->amount) ?></td>
          <td><?= $reward ?></td>
          <td><?= $address ?></td>
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

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){
    $('.exportcsv a').on('click', function(){
        alert('<?= $this->ee($this->text('dashboard-investors_table-disclaimer'), 'js') ?>');
    });
})

// @license-end
</script>

<?php $this->append() ?>

