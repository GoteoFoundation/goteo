<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
    <div class="container">
        <h1><?= $this->t('dashboard-project-subscription-title') ?></h1>

        <div class="row">
            <div class="col-md-10 col-sm-10 description">
                <?= $this->t('dashboard-project-subscription-description') ?>
            </div>
        </div>

        <?php if ($this->project->isPermanent()) { ?>
            <?= $this->supply('dashboard-content-form', function () {
                $form = $this->raw('form');
                echo $this->form_start($form);
            }) ?>
        <?php } else { ?>
            <div class="row">
                <div class="col-md-10 col-sm-10 description">
                    <?= $this->t('dashboard-project-subscription-unavailable') ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php $this->replace() ?>