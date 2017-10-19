<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">

    <h1><?= $this->text('personal-main-header') ?></h1>
    <p><?= $this->text('guide-project-contract-information') ?></p>

    <h2><?= $this->text('personal-field-contract_data') ?></h2>
    <p><?= $this->text('tooltip-project-contract_data') ?></p>

    <?= $this->insert('dashboard/project/partials/goto_first_error') ?>

    <?= $this->supply('dashboard-content-form', function() {
        $form = $this->raw('form');
        $ret = $this->form_start($form);

        foreach($form as $key => $row) {
            if(!in_array($key, ['paypal', 'bank', 'submit'])) {
                $ret .= $this->form_row($row);
            }
        }
        $ret .= '<h2>' . $this->text('personal-field-accounts') . '</h2>';
        $ret .= $this->form_rest($form);
        $ret .= $this->form_end($form);
        return $ret;
    }) ?>

    <?= $this->insert('dashboard/project/partials/partial_validation') ?>

  </div>
</div>

<?php $this->replace() ?>
