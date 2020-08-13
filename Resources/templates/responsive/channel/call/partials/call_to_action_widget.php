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
      <div class="col-button">
          <a href="<?= $this->cta->action_url ?>" class="btn btn-transparent"><i class="icon icon-<?= $this->cta->icon ?> icon-2x"></i><?= $this->cta->action ?></a>
      </div>
    </div>
  </div>
</div>
