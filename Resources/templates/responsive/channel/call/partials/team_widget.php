<div class="team_widget">
  <div class="image">
    <img loading="lazy" src='<?= $this->member->getImage()->getLink(200, 200, true) ?>'>
  </div>

  <div class="name">
    <?= $this->member->name ?>
  </div>

  <div class="role">
    <?= $this->member->role ?>
  </div>
</div>