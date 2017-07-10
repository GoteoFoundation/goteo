<?php
$this->layout('layout', [
    'bodyClass' => '',
    'title' => ($this->invest_title ? $this->invest_title : $this->text('invest-method-title')). ' :: Goteo.org',
    'meta_description' => $this->invest_description ? $this->invest_description : $this->text('invest-method-title')
    ]);

$this->section('content');

?>

    <?= $this->insert('invest/partials/project_info') ?>

    <?= $this->insert('invest/partials/steps_bar') ?>

    <?= $this->supply('main-content') ?>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

    <?= $this->insert('project/partials/google_analytics.php', ['project' => $this->project]) ?>
    <?= $this->insert('project/partials/facebook_pixel.php', ['project' => $this->project]) ?>

<?php $this->append() ?>

