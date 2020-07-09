<div class="section map">
  <div class="container">
    <div class="row">
      <h2 class="title"><span class="icon icon-news icon-3x"></span><?= $this->t('channel-call-map-section-title') ?></h2>

      <iframe src="<?= SRC_URL ?>/map?channel=<?= $this->channel->id ?>&height=350" width="100%" height="400" style="border:none;" allowfullscreen></iframe>
    </div>
  </div>
</div>