<?php

$this->layout('invest/layout');

$this->section('main-content');


?>

<div class="container">

    <div class="row row-form">
        <div class="panel panel-default invest-container">
            <div class="panel-body">
                <h2 class="padding-bottom-2"><?= $this->text('invest-method-title') ?></h2>

                <div class="reminder">
                    <p class="level-1">
                       <?= $this->text('invest-alert-investing') ?><span class="amount-reminder"><?= $this->raw('amount_formated') ?></span> <?= $this->text('invest-project') ?> <span class="uppercase"><?= $this->ee($this->project->name) ?></span>
                    </p>
                    <?php if($this->reward): ?>
                        <p class="level-2">
                            <?= $this->text('invest-alert-rewards') ?>
                            <strong class="text-uppercase"><?= $this->reward->reward ?></strong>
                        </p>
                    <?php endif ?>
                </div>

                <form role="form" method="GET" action="/invest/<?= $this->project->id ?>/form">
                    <input type="hidden" name="reward" value="<?= $this->reward ? $this->reward->id : '0' ?>">
                    <input type="hidden" name="amount" value="<?= $this->amount_original . $this->currency ?>">
                    <input type="hidden" id="project_amount" name="project_amount" value="<?= $this->amount_original ?>">
                    <input type="hidden" id="currency" name="currency" value="<?= $this->get_currency('html') ?>">

                    <fieldset>
                        <legend><?= $this->t('invest-payment_method-header') ?></legend>
                        <div class="row pay-methods">
                            <?php foreach($this->pay_methods as $method => $pay): ?>
                                <div class="col-xxs-6 col-tn-6 col-xs-3 pay-method<?= $pay->isActive() ? '' : ' disabled' ?>">
                                    <label class="label-method <?= $pay->isActive($this->amount) ? '' : 'label-disabled' ?> <?= $this->default_method == $method ? ' method-choosen' : '' ?>" for="<?= $method ?>-method">
                                        <input class="method" name="method" id="<?= $method ?>-method"<?= $this->default_method == $method ? ' checked' : '' ?> <?= $pay->isActive($this->amount) ? '' : ' disabled="disabled"' ?> value="<?= $method ?>" type="radio">
                                        <span class="method-text">
                                            <?= $pay->getName() ?>
                                        </span>
                                        <img class="img-responsive img-method" alt="<?= $method ?>" title="<?= $pay->getDesc() ?>" src="<?= $pay->getIcon() ?>">
                                    </label>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </fieldset>

                    <?php if($this->skip_login): ?>
                        <?= $this->insert('invest/partials/noregister_form') ?>
                    <?php endif ?>


                    <div class="form-group">
                        <div class="checkbox no-tip">
                            <label>
                                <input class="no-margin-checkbox big-checkbox" type="checkbox" name="anonymous" id="anonymous" value="1"<?= $this->skip_login && !$this->name ? ' checked="checked"' : ''?>>
                                    <p class="label-checkbox">
                                    <?= $this->text('invest-anonymous') ?>
                                    </p>
                            </label>

                        </div>

                        <?php if(!$this->skip_login && array_key_exists('pool', $this->pay_methods)): ?>
                        <div class="checkbox no-tip">
                            <label>
                                <input class="no-margin-checkbox big-checkbox" type="checkbox" name="pool_on_fail" id="pool_on_fail" value="1">
                                    <p class="label-checkbox">
                                    <?= $this->text('invest-pool') ?><a data-toggle="modal" data-target="#myModal" href=""> <?= $this->text('invest-more-info') ?></a>
                                    </p>
                            </label>
                        </div>
                        <?php endif ?>

                        <?php if($this->tip): ?>
                        <div class="checkbox">
                            <label class="tip">
                                <input class="no-margin-checkbox big-checkbox" autocomplete="off" type="checkbox" name="tip" id="tip" value="1"<?= $this->donate_amount ? ' checked="checked"' : ''?> >
                                <div class="label-checkbox">
                                    <div class="txt-1"><?= $this->text('invest-tip-part-1') ?></div>
                                    <div class="input-container">
                                        <div class="input-group">
                                                <div class="input-group-addon"><?= $this->get_currency('html') ?></div>
                                                <input type="number" min="0" class="form-control input-md input-amount" name="donate_amount" value="<?= $this->donate_amount ? amount_format($this->donate_amount, 0, true) : amount_format($this->get_config('donate.tip_amount'), 0, true) ?>" id="donate_amount" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="txt-2">
                                        <?= $this->project->tip_msg ? $this->project->tip_msg : $this->text('invest-tip-part-2') ?>
                                    </div>
                                </div>
                            </label>

                        </div>

                        <?php $total_amount=amount_format($this->amount, 0, true)+amount_format($this->donate_amount, 0, true); ?>

                        <div class="tip-message" style="<?= $this->donate_amount ? '' : 'display:none;' ?>" id="tip-message">
                           <?= $this->text('invest-update-total', ['%TOTAL_AMOUNT%' => $this->get_currency('html').' '.$total_amount, '%PROJECT_AMOUNT%' => amount_format($this->amount), '%DONATE_AMOUNT%' => amount_format($this->donate_amount) ]) ?>
                        </div>
                        <?php endif; ?>

                    </div>


                    <div class="form-group">
                        <button type="submit" class="btn btn-lg btn-block btn-cyan"><i class="fa fa-download"></i> <?= $this->text('invest-button') ?></button>
                    </div>
                </form>
            </div>
        </div>

    </div>


    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $this->text('invest-modal-pool-title') ?></h4>
      </div>
      <div class="modal-body">
        <?= $this->text('invest-modal-pool-description') ?>
      </div>
    </div>
  </div>
</div>

<?php $this->replace() ?>

<?php $this->section('facebook-pixel') ?>
    <?= $this->insert('partials/facebook_pixel', ['pixel' => $this->project->facebook_pixel, 'track' => ['PageView', 'InitiateCheckout']]) ?>
<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function(){

<?php if($this->skip_login): ?>
    $('input[name="name"]').on('change', function() {
        var $anon = $('input[name="anonymous"]');
        if($(this).val().trim().length) {
            $anon.prop('checked', false);
        }
    });
<?php elseif(array_key_exists('pool', $this->pay_methods)): ?>
    $('#pool_on_fail').get(0).originalStatus = $('#pool_on_fail').prop('checked');

    $('input[name="method"]').on('change', function(){
        if($(this).val() === 'pool') {
            $('#pool_on_fail').prop('checked', true);
            $('#pool_on_fail').prop('disabled', true);
        }
        else {
            $('#pool_on_fail').prop('disabled', false);
            if(typeof $('#pool_on_fail').get(0).originalStatus !== 'undefined') {
                $('#pool_on_fail').prop('checked', $('#pool_on_fail').get(0).originalStatus);
            }
        }
    });
    $('#pool_on_fail').on('change', function(){
        $(this).get(0).originalStatus = $(this).prop('checked');
    });
<?php endif ?>

});



$(':radio').change(function(){
    var id = $(this).attr('id');
    $(this).closest( "label" ).addClass( "method-choosen" );
    $(".method:not(#" + id + ")").each(function(){
        $(this).closest( "label" ).removeClass( "method-choosen" );
        $(this).prop('checked', false);
    })

});


$('input#tip').change(function(){
    if ($(this).is(":checked")) {
        $('#tip-message').show();
        var total=parseInt($('input#donate_amount').val())+parseInt($('#project_amount').val());
       $('#total-amount').html($('#currency').val()+' '+total);
       $('#tip-amount').html($('#currency').val()+' '+$('input#donate_amount').val());

    }
    else
        $('#tip-message').hide();

});

//Update tip message

$('input#donate_amount').change(function(){
   var total=parseInt($(this).val())+parseInt($('#project_amount').val());
   $('#total-amount').html($('#currency').val()+' '+total);
   $('#tip-amount').html($('#currency').val()+' '+$(this).val());
});

// @license-end
</script>
<?php $this->append() ?>
