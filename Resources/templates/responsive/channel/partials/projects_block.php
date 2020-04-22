<?= 'Num elements: '.$this->num_elements ?> <?php die; ?>
<?php if ($this->projects) : ?>
    <?php foreach ($this->projects as $project): ?>
        <div class="<?= if($this->num_elements=4) ? 'col-md-3' : 'col-md-4'?> col-sm-6 col-xs-12 spacer widget-element">
            <?= $this->insert('project/widgets/normal', [
                'project' => $project,
                'admin' => (bool)$this->admin
            ]) ?>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <?= $this->text('discover-results-empty') ?>
<?php endif ?>
