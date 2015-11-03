<div class="jumbotron project-info">
    <div class="container">
      <div class="row invest-container">       
          <h3 class="project-name"><?= $this->project->name ?></h3>
          <div class="project-subtitle">
          <?= $this->project->subtitle ?>
          </div>
          <div class="project-owner pull-left">
          	<a href="/user/profile/<?= $this->project->user->id ?>"><?= $this->text('regular-by')." ". $this->project->user->name ?></a>
        	</div>
          <?php if (!empty($this->project->cat_names)) : ?>
            <div class="project-tags pull-left hidden-xs hidden-sm">
            <?php $sep = '';
            foreach ($this->project->cat_names as $key=>$value) :
            echo $sep.'<a href="/discover/results/'.$key.'/'.$value.'">'.htmlspecialchars($value).'</a>';
                 $sep = ', ';
                  endforeach; ?>
            </div>
          <?php endif; ?>       
      </div>
    </div>
</div>
