<?php
  $team = $this->channel->getTeam();

  if ($team):
?>

<div class="section team">
  <div class="container">
    <h2 class="title">
      <span class="icon icon-team icon-3x"></span>
      <span><?= $this->t('channel-call-team-title') ?></span>
    </h2>

    <div class="description">
      <?= $this->t('channel-call-team-description') ?>
    </div>

    <div class="row spacer-20">
      <?php foreach($team as $team_member): ?>
        <div class="col-md-3 col-sm-4 col-xs-6">
          <?= $this->insert('channel/call/partials/team_widget', ['member' => $team_member]) ?>
        </div>
      <?php endforeach ?>
    </div>
  </div>
</div>

<?php endif; ?>