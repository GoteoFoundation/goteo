<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1><?= $this->project->isApproved() ? '' : '5. ' ?><?= $this->text('rewards-main-header') ?></h1>
    <div class="auto-hide">
        <div class="inner"><?= $this->text('guide-project-rewards') ?></div>
        <!-- <div class="more"><i class="fa fa-info-circle"></i> <?= $this->text('regular-help') ?></div> -->
    </div>

    <?= $this->insert('dashboard/project/partials/goto_first_error') ?>

    <?= $this->supply('dashboard-content-form', function() {
        $form = $this->raw('form');
        echo $this->form_start($form);

        $submit = $this->form_row($form['submit']);
        echo '<div class="top-button hidden">' . $submit . '</div>';

        // echo $this->form_row($form['title-rewards']);

        echo '<div class="reward-list">';
        foreach($this->rewards as $reward) {
            echo $this->insert('dashboard/project/partials/reward_item', ['reward' => $reward, 'form' => $form, 'show_taken' => $this->project->isApproved()]);
        }
        echo '</div>';

        echo '<div class="form-group pull-right">'.$this->form_row($form['add-reward'], [], true).'</div>';

        echo $submit;

        echo $this->form_end($form);

    }) ?>

    <?= $this->insert('dashboard/project/partials/partial_validation') ?>

  </div>
</div>

<?php $this->replace() ?>

