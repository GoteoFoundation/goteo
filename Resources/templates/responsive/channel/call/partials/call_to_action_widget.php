<div class="item col-md-6 col-sm-6 col-xs-12">
  <div class="cta <?= $this->cta->style?>" >
    <img class="img-responsive" src="<?= $this->cta->header->getLink(500,300)?>" >
    <div class="info">
      <div class="title">
        <?= $this->cta->title ?>
      </div>
      <div class="description">
        <?= $this->cta->description ?>
      </div>
      <?php if ($this->cta->action_url_2): ?>
        <div class="row">
          <div class="col-button col-md-6">
            <a href="<?= $this->cta->action_url ?>" class="btn btn-transparent"><i class="icon icon-<?= $this->cta->icon ?> icon-2x"></i><?= $this->cta->action ?></a>
          </div>
          <div class="col-button col-md-6">
              <a href="<?= $this->cta->action_url_2 ?>" class="btn btn-transparent"><i class="icon icon-<?= $this->cta->icon_2 ?> icon-2x"></i><?= $this->cta->action_2 ?></a>
          </div>
      </div>
      <?php else: ?>
        <div class="col-button">
            <a href="<?= $this->cta->action_url ?>" class="btn btn-transparent"><i class="icon icon-<?= $this->cta->icon ?> icon-2x"></i><?= $this->cta->action ?></a>
          </div>
      <?php endif; ?>
    </div>
  </div>
</div>
