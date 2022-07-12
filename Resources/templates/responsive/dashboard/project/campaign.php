<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">

    <h1>6. <?= $this->text('project-campaign-configuration') ?></h1>

    <?= $this->insert('dashboard/project/partials/goto_first_error') ?>

    <?= $this->supply('dashboard-content-form') ?>

    <?= $this->form_form($this->raw('form')) ?>

    <?= $this->insert('dashboard/project/partials/partial_validation') ?>

  </div>
</div>

<?php $this->replace() ?>
<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){

    $('#autoform_one_round input[type="radio"]').on('change', function() {
        var $help = $(this).closest('.input-wrap').find('.help-text');
        $active = $help.find('span').eq(1-$(this).val()).removeClass('hidden');
        $help.find('span').not($active).addClass('hidden');
    });
});

// @license-end
</script>
<?php $this->append() ?>
