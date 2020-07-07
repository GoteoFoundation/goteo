<div class="section map">
  <div class="container">
    <div class="row">
      <div class="col-md-6">

        <h2 class="title"><span class="icon icon-news icon-3x"></span><?= $this->t('channel-call-map-section-title') ?></h2>

        <div class="description">
          <?= $this->t('channel-call-map-section-description') ?>
        </div>

        <?=
          $this->map->map();
        ?>
      </div>

      <div class="col-md-6">
        <h2 class="title"><span class="icon icon-news icon-3x"></span><?= $this->t('channel-call-map-section-data-title') ?></h2>

        <div class="description">
          <?= $this->t('channel-call-map-section-data-description') ?>
        </div>
      </div>
    </div>
  </div>
</div>

