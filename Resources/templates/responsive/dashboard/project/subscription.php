<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
    <div class="inner-container">
        <h1><?= $this->t('dashboard-project-subscription-title') ?></h1>
        <div class="auto-hide">
            <div class="inner">
                <strong><?= $this->t('dashboard-project-subscription-description') ?></strong>
            </div>
            <!-- <div class="more"><i class="fa fa-info-circle"></i> <?= $this->text('regular-help') ?></div> -->
        </div>

        <?php if ($this->project->isPermanent()) { ?>
            <?= $this->insert('dashboard/project/partials/goto_first_error') ?>
            <?= $this->supply('dashboard-content-form', function () {
                $form = $this->raw('form');
                echo $this->form_start($form);

                $submit = $this->form_row($form['submit']);
                echo '<div class="top-button hidden">' . $submit . '</div>';

                echo '<div class="reward-list">';
                foreach ($this->subscriptions as $subscription) {
                    echo $this->insert(
                        'dashboard/project/partials/subscription_item',
                        ['subscription' => $subscription, 'form' => $form, 'show_taken' => $this->project->isApproved()]
                    );
                }
                echo '</div>';

                echo '<div class="form-group pull-right">' . $this->form_row($form['add-subscription'], [], true) . '</div>';

                echo $submit;

                echo $this->form_end($form);
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