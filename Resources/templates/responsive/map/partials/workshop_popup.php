<div class="workshop">
  <div class="left">
    <a target="_blank" href="/workshop/<?= $this->workshop->id ?>">
      <img src="<?= $this->workshop->getHeaderImage()->getLink(120,120) ?>">
    </a>
  </div>
  <div class="right">
    <h4><?= $this->workshop->title ?> </h4>
  </div>
</div>
