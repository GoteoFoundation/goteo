<?php

$this->layout('widget/layout', [
    'title' => $this->ee($this->project->name),
    'meta_description' => $this->ee($this->project->subtitle)
    ]);

$this->section('content');
?>

<div style="max-width: 300px">
    <?php if ($this->project->isPermanent()): ?>
        <?= $this->insert('project/widgets/normal_permanent', ['project' => $this->project]) ?>
    <?php else: ?>
        <?= $this->insert('project/widgets/normal', ['project' => $this->project]) ?>
    <?php endif; ?>
</div>

<?php $this->replace() ?>
