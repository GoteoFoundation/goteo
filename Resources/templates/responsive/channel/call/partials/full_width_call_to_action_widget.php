<div class="cta <?= $this->cta->style?> col-center" style="<?= $this->colors['secondary'] ? "background-color:".$this->colors['secondary'] : '' ?>" >
  <div class="col-md-6">
    <img class="img-responsive" src="<?= $this->cta->header->getLink(500,300)?>" >
  </div>
  <div class="col-md-6">
    <div class="info">
      <div class="title">
        <?= $this->cta->title ?>
      </div>
      <div class="description">
        <?= $this->cta->description ?>
      </div>

      <?php if ($this->cta->action_url_2): ?>
          <div class="col-button col-md-6">
            <a href="<?= $this->cta->action_url ?>" class="btn btn-transparent"><i class="icon icon-<?= $this->cta->icon ?> icon-2x"></i><?= $this->cta->action ?></a>
          </div>
          <div class="col-button col-md-6">
            <a href="<?= $this->cta->action_url_2 ?>" class="btn btn-transparent hidden-xs"><i class="icon icon-<?= $this->cta->icon_2 ?> icon-2x"></i><?= $this->cta->action_2 ?></a>
          </div>
      <?php else: ?>
        <div class="col-button">
            <a href="<?= $this->cta->action_url ?>" class="btn btn-transparent"><i class="icon icon-<?= $this->cta->icon ?> icon-2x"></i><?= $this->cta->action ?></a>
          </div>
      <?php endif; ?>
    </div>
  </div>
</div>