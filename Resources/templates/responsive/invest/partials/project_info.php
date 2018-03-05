<div class="jumbotron project-info">
    <div class="container">
      <div class="row invest-container">
          <h3 class="project-name"><a href="/project/<?= $this->project->id ?>"><?= $this->project->name ?></a></h3>
          <div class="project-subtitle">
          <?= $this->project->subtitle ?>
          </div>
          <div class="project-owner pull-left">
          	<a href="/user/profile/<?= $this->project->user->id ?>"><?= $this->text('regular-by')." ". $this->project->user->name ?></a>
        	</div>
          <?php if ($this->project_categories) : ?>
            <div class="project-tags pull-left hidden-xs hidden-sm">
            <?php $sep = '';
            foreach ($this->project_categories as $key => $value) :
            echo $sep . '<a href="/discover?category=' . $key . '">' . $value . '</a>';
                 $sep = ', ';
                  endforeach ?>
            </div>
          <?php endif ?>
      </div>
    </div>
</div>
