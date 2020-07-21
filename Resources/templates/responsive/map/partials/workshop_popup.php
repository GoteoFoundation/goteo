<div class="workshop">
  <div class="left">
    <a href="/workshop/<?= $this->workshop->id ?>">
      <img src="<?= $this->workshop->getHeaderImage()->getLink(120,120) ?>">
    </a>
  </div>
  <div class="right">
    <h3><?= $this->workshop->title ?> </h3>
  </div>
</div>
