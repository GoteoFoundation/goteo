<?php

$this->layout('widget/layout', [
    'title' => $this->ee($this->project->name),
    'meta_description' => $this->ee($this->project->subtitle)
    ]);

$this->section('content');
?>

<div style="max-width: 300px">
    <?= $this->insert('project/widgets/normal', ['project' => $this->project]) ?>
</div>

<?php $this->replace() ?>
