<div class="jumbotron" style="background-color: #E9F4EA; color: #58595B; padding-top:20px; padding-bottom:20px;">
    <div class="container">
      <div class="col-md-2 col-md-offset-3">
      <a href="<?php echo $url ?>/project/<?= $this->project->id ?>"><img class="img-responsive" src="<?= $this->project->image->getLink(150, 98, true) ?>" alt="<?= $this->project->name ?>"/></a>
      </div>
      <div class="col-md-7">
        <h3 style="padding-top: 0; margin-top: 10px;"><?= $this->project->name ?></h3>
        <?= $this->text('regular-by') ?> <a href="/user/profile/<?php echo htmlspecialchars($this->project->user->id) ?>"><?php echo htmlspecialchars($this->project->user->name) ?></a>
      </div>
    </div>
</div>        
