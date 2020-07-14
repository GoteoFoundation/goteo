<div class="project">
  <div class="top">
    <div class="left">
      <a href="<?= SRC_URL + '/project/' + $this->project->id ?>"><img src="<?= $this->project->image ?>"></a>
    </div>
    <div class="right">
      <h3><?= $this->project->name ?> </h3>
    </div>
  </div>
  <div class="bottom">
    <div class="left">
      <strong><?= amount_format($this->project->amount) ?></strong><br><?= $this->text('horizontal-project-reached') ?>
    </div>
    <div class="left">
      <strong><?= amount_format($this->project->mincost) ?></strong><br><?= $this->text('project-view-metter-minimum') ?>
    </div>
    <div class="left">
      <strong><?= amount_format($this->project->maxcost) ?></strong><br><?= $this->text('project-view-metter-optimum') ?>
    </div>
  </div>
</div>
