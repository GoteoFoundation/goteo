<div class="section workshops">
  <div class="container workshops">
      <h2 class="title"><span class="icon icon-bell icon-3x"></span><?= $this->t('channel-call-workshop-title') ?></h2>
          
      <div class="description">
        <?= $this->t('channel-call-workshop-description') ?>
      </div>
    <?= $this->channel->getWorkshops() ? $this->insert('channel/call/partials/workshops_slider', [
        'workshops' => $this->channel->getAllWorkshops()
    ]) : '' ?>
  </div>
</div>