<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>
<?php
$form = $this->raw('form');
?>
<div class="dashboard-content">
  <div class="inner-container">

    <h1><?= $this->text('personal-main-header') ?></h1>
    <p><?= $this->text('guide-project-contract-information') ?></p>

    <h2><?= $this->text('personal-field-contract_data') ?></h2>
    <p><?= $this->text('tooltip-project-contract_data') ?></p>
    <?= $this->form_start($form) ?>
    <?php
    foreach($form as $key => $row) {
        if(!in_array($key, ['paypal', 'bank', 'submit'])) {
            echo $this->form_row($row);
        }
    }
    ?>

    <h2><?= $this->text('personal-field-accounts') ?></h2>
    <?= $this->form_rest($form) ?>
    <?= $this->form_end($form) ?>


  </div>
</div>

<?php $this->replace() ?>
