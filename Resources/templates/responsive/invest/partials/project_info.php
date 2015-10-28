<div class="jumbotron project-info">
    <div class="container">
      <div class="row select-method">
        <div class="col-md-3 col-md-offset-1">
          <a href="<?= $this->url ?>/project/<?= $this->project->id ?>"><img class="img-responsive" src="<?= $this->project->image->getLink(150, 98, true) ?>" alt="<?= $this->project->name ?>"/></a>
        </div>
        <div class="col-md-7">
          <h3 class="project-name"><?= $this->project->name ?></h3>
          <div class="project-owner">
          	<?= $this->text('regular-by') ?> <a href="/user/profile/<?= $this->project->user->id ?>"><?= $this->project->user->name ?></a>
        	</div>
        </div>
      </div>
    </div>
</div>
