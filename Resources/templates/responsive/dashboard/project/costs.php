<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1><?= $this->text('costs-main-header') ?></h1>
    <p><?= $this->text('guide-project-costs') ?></p>

    <?= $this->supply('dashboard-content-form', function() {return $this->form_form($this->raw('form'));}) ?>

  </div>
</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){
    $('#autoform_one_round input[type="radio"]').on('change', function() {
        var $help = $(this).closest('.input-wrap').find('.help-text');
        if($(this).val() == 0) {

        }
        $active = $help.find('span').eq(1-$(this).val()).removeClass('hidden');
        $help.find('span').not($active).addClass('hidden');
    });
});

// @license-end
</script>
<?php $this->append() ?>
