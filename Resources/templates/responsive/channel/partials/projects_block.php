<?php if ($this->projects) : ?>
	<div class="row">
    <?php foreach ($this->projects as $project): ?>
        <div class="col-md-4 col-sm-6 col-xs-12 spacer widget-element">
            <?= $this->insert('project/widgets/normal', [
                'project' => $project,
                'admin' => (bool)$this->admin
            ]) ?>
        </div>
    <?php endforeach; ?>
	</div>
<?php else : ?>
    <?= $this->text('discover-results-empty') ?>
<?php endif ?>
