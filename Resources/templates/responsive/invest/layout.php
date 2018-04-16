<?php
$this->layout('layout', [
    'bodyClass' => '',
    'title' => ($this->alt_title ? $this->alt_title : $this->text('invest-method-title')). ' :: Goteo.org',
    'meta_description' => $this->alt_description ? $this->alt_description : $this->text('invest-method-title')
    ]);

$project = $this->project ? $this->project : $project = $this->get_session('current_project');
$step = $this->step ? $this->step : $step = $this->get_session('current_step');

$this->section('content');

?>

    <?= $this->insert('invest/partials/project_info', ['project' => $project]) ?>

    <?= $this->insert('invest/partials/steps_bar', ['step' => $step]) ?>

    <?= $this->supply('main-content') ?>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

    <?= $this->supply('facebook-pixel', $this->insert('partials/facebook_pixel', ['pixel' => $this->project->facebook_pixel])) ?>

<?php $this->append() ?>

