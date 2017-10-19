<?php

$this->layout('dashboard/layout', [
    'bodyClass' => 'dashboard',
    'title' => $this->text('meta-title-pool-method'),
    'meta_description' => $this->text('invest-method-title')
    ]);

$this->section('dashboard-content');

?>

    <?= $this->insert('pool/partials/steps_bar') ?>

    <div class="dashboard-content cyan">
      <div class="inner-container">
        <div class="panel panel-default">
          <div class="panel-body">

            <?= $this->supply('dashboard-content-pool') ?>

          </div>
        </div>
      </div>
    </div>

<?php $this->replace() ?>
