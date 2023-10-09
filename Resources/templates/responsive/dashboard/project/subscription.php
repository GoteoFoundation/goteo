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
        </div>
    </div>

<?php $this->replace() ?>
