<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">

    <h1>6. <?= $this->text('project-campaign-configuration') ?></h1>
<!--     <div class="auto-hide">
        <div class="inner"><?= $this->text('guide-campaign-description') ?></div>
        <div class="more"><i class="fa fa-info-circle"></i> <?= $this->text('regular-help') ?></div>
    </div>
 -->

    <?= $this->insert('dashboard/project/partials/goto_first_error') ?>

    <?= $this->supply('dashboard-content-form', function() {
        $form = $this->raw('form');
        echo $this->form_start($form);

        echo $this->form_row($form['one_round']);

        echo $this->form_row($form['phone']);

        echo '<div class="form-group spacer-10">
          <div class="material-switch">
              <input id="paypal_switch" type="checkbox"' . ($form['paypal']->vars['value'] ? ' checked="true"' : '') . '>
              <label for="paypal_switch" class="label-cyan"></label>
          </div>
          <label for="paypal_switch"><i class="fa fa-paypal"></i> ' . $this->text('project-campaign-use-paypal') . '</label>
          </div>';

        echo '<div class="paypal' . ($form['paypal']->vars['value'] ? ' show' : '') . '">';
            echo $this->form_row($form['paypal']);
        echo '</div>';

        echo $this->form_end($form);


    }) ?>

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


    $('#paypal_switch').on('change', function() {
        $paypal = $(this).closest('.form-group').next('.paypal');
        // console.log('switch', $paypal.attr('class'));
        if($(this).prop('checked')) {
            $paypal.addClass('show');
        } else {
            $paypal.removeClass('show');
            $paypal.find('input[type="email"]').val(' ');
            $paypal.find('input[type="email"]').select();
        }
    });
});

// @license-end
</script>
<?php $this->append() ?>
