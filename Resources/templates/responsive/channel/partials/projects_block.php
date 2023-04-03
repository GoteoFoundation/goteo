<?php if ($this->projects) : ?>
	<div class="row">
    <?php foreach ($this->projects as $project): ?>
        <div class="col-md-4 col-sm-6 col-xs-12 spacer widget-element">
            <?php if ($project->isPermanent()): ?>
                <?= $this->insert('project/widgets/normal_permanent', [
                    'project' => $project,
                    'admin' => (bool)$this->admin
                ]) ?>
            <?php else: ?>
                <?= $this->insert('project/widgets/normal', [
                    'project' => $project,
                    'admin' => (bool)$this->admin
                ]) ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
	</div>
<?php else : ?>
    <?= $this->text('discover-results-empty') ?>
<?php endif ?>
