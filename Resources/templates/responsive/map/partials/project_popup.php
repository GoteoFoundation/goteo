<div class="project">
  <div class="top">
    <a class="left" target="_blank" href="/project/<?= $this->project->id ?>"><img src="<?= $this->project->image->getLink(118,73) ?>"></a>
    <div class="right">
      <div  ><?= $this->project->name ?> </div  >
    </div>
  </div>
  <div class="bottom">
    <div>
      <strong><?= amount_format($this->project->amount) ?></strong><br><?= $this->text('horizontal-project-reached') ?>
    </div>
    <div>
      <strong><?= amount_format($this->project->mincost) ?></strong><br><?= $this->text('project-view-metter-minimum') ?>
    </div>
    <div>
      <strong><?= amount_format($this->project->maxcost) ?></strong><br><?= $this->text('project-view-metter-optimum') ?>
    </div>
  </div>
</div>
