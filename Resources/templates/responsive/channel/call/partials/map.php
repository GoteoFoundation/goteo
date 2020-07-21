<div class="section map">
  <div class="container">
    <div class="row">
      <h2 class="title"><span class="icon icon-news icon-3x"></span><?= $this->t('channel-call-map-section-title') ?></h2>

      <?php 
        $summary = $this->channel->getSummary();
      ?>
      <div class="row impact-data">
        <div class="col-sm-4 col-md-4 item">
            <span><?= amount_format($summary['amount']) ?></span>
            <div class="description">
                <?= $this->t('channel-call-impact-data-amount') ?>
            </div>
        </div>
        <div class="col-sm-3 col-md-4 item">
              <span><?= $summary['projects'] ?></span>
            <div class="description">
                <?= $this->t('channel-call-impact-data-projects')  ?>
            </div>
        </div>
        <div class="col-sm-4 col-md-4 item">
            <span> <?= $summary['investors'] ?></span>
            <div class="description">
              <?= $this->t('channel-call-impact-data-investors') ?>
            </div>
        </div>
      </div>

      <iframe src="/map?channel=<?= $this->channel->id ?>" width="100%" height="500" style="border:none;" allowfullscreen></iframe>
    </div>
  </div>
</div>