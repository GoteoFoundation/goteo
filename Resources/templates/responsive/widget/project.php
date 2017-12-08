<?php

$this->layout('widget/layout', [
    'title' => $this->project->name,
    'meta_description' => $this->project->subtitle
    ]);

$this->section('content');
?>

<div style="max-width: 300px">
    <?= $this->insert('project/widgets/normal', ['project' => $this->project]) ?>
</div>

<?php $this->replace() ?>
