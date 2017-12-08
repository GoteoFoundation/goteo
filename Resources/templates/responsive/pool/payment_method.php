<?php

$this->layout('pool/layout');

$this->section('dashboard-content-pool');

?>
<div class="pool-container">

	<h2><?= $this->text('pool-pay-method-title') ?></h2>

	<div class="reminder">
        <div class="level-1">
	       <?= $this->text('pool-alert-recharging') ?><span class="amount-reminder"><?= $this->raw('amount_formated') ?></span>
		</div>
	</div>

    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

    <form class="form-horizontal" role="form" method="GET" action="/pool/form">
    <input type="hidden" name="amount" value="<?= $this->amount_original . $this->currency ?>">

	<div class="row no-padding">

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

        <div class="form-group">
            <div class="col-md-4 invest-button">
                <button type="submit" class="btn btn-lg btn-cyan"><i class="fa fa-download"></i> <?= $this->text('recharge-button') ?></button>
            </div>
        </div>
    </form>

</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

    $(':radio').change(function(){
        var id = $(this).attr('id');
        $(this).closest( "label" ).addClass( "method-choosen" );
        $(".method:not(#" + id + ")").each(function(){
            $(this).closest( "label" ).removeClass( "method-choosen" );
            $(this).prop('checked', false);
        })

    });

// @license-end
</script>
<?php $this->append() ?>
