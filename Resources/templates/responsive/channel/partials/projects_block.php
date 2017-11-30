<?php if ($this->projects) : ?>
    <?php foreach ($this->projects as $project): ?>
        <div class="col-sm-6 col-md-4 col-xs-12 spacer widget-element">
            <?= $this->insert('project/widgets/normal', [
                'project' => $project,
                'admin' => (bool)$this->admin
            ]) ?>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <?= $this->text('discover-results-empty') ?>
<?php endif ?>
