<?php if($this->projects): ?>
    <?php foreach ($this->projects as $project) : ?>
      <div class="col-sm-6 col-md-4 col-xs-12 spacer widget-element">
        <?= $this->insert('project/widgets/standard', ['project' => $project, 'admin' => $project->userCanEdit($this->get_user())]) ?>
      </div>
    <?php endforeach ?>
<?php endif ?>
