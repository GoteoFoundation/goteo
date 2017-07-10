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

    <?php $this->section('main-content') ?>

        <div class="container">
            <div class="row row-form">
                <div class="panel panel-default invest-container">
                    <div class="panel-body">
                        <?= $this->supply('inner-content', 'def') ?>
                    </div>
                </div>
            </div>
        </div>

    <?php $this->stop() ?>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

    <?= $this->insert('project/partials/google_analytics.php', ['project' => $project]) ?>
    <?= $this->insert('project/partials/facebook_pixel.php', ['project' => $project]) ?>

<?php $this->append() ?>

