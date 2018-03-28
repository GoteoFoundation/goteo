<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>
<div class="dashboard-content">

  <div class="inner-container">
<?php if($this->invests): ?>
    <h2><?= $this->text('dashboard-rewards-my-invests') ?></h2>
    <p><?= $this->text('dashboard-rewards-invests-totals', ['%RAISED%' => \amount_format($this->raised), '%RETURNED%' => \amount_format($this->returned), '%TOTAL%' => $this->total]) ?></p>
    <?php if($this->wallet): ?>
        <p><a href="/dashboard/wallet"><?= $this->text('dashboard-rewards-invests-wallet', ['%AMOUNT%' => \amount_format($this->wallet)]) ?></a></p>
    <?php endif ?>
    <table class="table">
    <?php
    foreach($this->invests as $invest):
        $project = $invest->getProject();
        $project = $project ? ('<a href="/project/' . $project->id .'">' . $project->name . '</a>') : '<span class="label label-info">'.$this->text('invest-status-to-pool').'</span>';
        $reward = [];
        $rewards = $invest->getRewards();
        if($rewards) {
            foreach($rewards as $r) {
                $reward[] = $r->reward;
            }
        }
        $class1 = (!$invest->project || $invest->isCharged()) ? '' : ' class="strikethrough"';
        $class2 = 'danger';
        if($invest->inPool()) $class2 = 'info';
        elseif($invest->isCharged()) $class2 = 'success';
    ?>
    <tr>
        <td><?= $invest->id ?></td>
        <td><?= \date_formater($invest->invested) ?></td>
        <td><?= $invest->getMethod()->getName() ?></td>
        <td<?= $class1 ?>><?= \amount_format($invest->amount) ?></td>
        <td<?= $class1 ?>><?= $project ?></td>
        <td<?= $class1 ?>><?= implode(",", $reward) ?></td>
        <td><span class="label label-<?= $class2 ?>"><?= $invest->getStatusText() ?></span></td>
    </tr>
  <?php endforeach ?>
    </table>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10]) ?>

<?php else: ?>
    <blockquote><?= $this->text('dashboard-rewards-no-invests') ?></blockquote>
<?php endif ?>
  </div>
</div>

<?php $this->replace() ?>

