<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">


    <h1>2. <?= $this->text('overview-main-header') ?></h1>
    <div class="auto-hide">
        <div class="inner"><?= $this->text('guide-project-description') ?></div>
        <div class="more"><i class="fa fa-info-circle"></i> <?= $this->text('regular-help') ?></div>
    </div>

    <?= $this->insert('dashboard/project/partials/goto_first_error') ?>

    <?= $this->supply('dashboard-content-form', function() {return $this->form_form($this->raw('form'));}) ?>

    <?= $this->insert('dashboard/project/partials/partial_validation') ?>

  </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script>

$(function(){
  $('#autoform_sign_check').change(function() {
    $('#form-sign_url').toggleClass('hidden');
    $('#form-sign_url_action').toggleClass('hidden');

    if (! $(this).is(':checked') ) {
      $('#autoform_sign_url').val('');
      $('#autoform_sign_url').prop('required', false);
      $('#autoform_sign_url_action').val('');
      $('#autoform_sign_url_action').prop('required', false);
    } else {
      $('#autoform_sign_url').prop('required', true);
      $('#autoform_sign_url_action').prop('required', true);
    }
  })
});

</script>
<?php $this->append() ?>