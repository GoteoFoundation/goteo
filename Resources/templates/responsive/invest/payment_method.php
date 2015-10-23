<?php

$this->layout('layout', [
    'bodyClass' => '',
    'title' => 'Realizar aporte :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

?>

<?= $this->insert('invest/partials/project_info') ?>

<div class="container">

	<div class="row row-form">
			<div class="panel panel-default select-method">
				<div class="panel-body">
					<h2 class="col-md-offset-1 padding-bottom-2">Realiza tu aporte</h2>

					<div class="col-md-10 col-md-offset-1 reminder">
					<?= $this->text('invest-alert-investing') ?><span class="amount-reminder"><?= amount_format($this->amount) ?></span> <?= $this->text('invest-project') ?> <span class="uppercase"><?= $this->project->name ?></span>
						<?php if($this->reward): ?>
						<div>
							<?= $this->text('invest-alert-rewards') ?>
	                        <strong class="uppercase"><?= $this->reward->reward ?></strong>
						</div>
					<?php endif; ?>
					</div>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>

                    <form class="form-horizontal" role="form" method="GET" action="/invest/<?= $this->project->id ?>/form">
                    <input type="hidden" name="reward" value="<?= $this->reward ? $this->reward->id : '0' ?>">
                    <input type="hidden" name="amount" value="<?= $this->amount ?>">

					<div class="row no-padding col-md-10 col-md-offset-1">

                    <?php foreach($this->pay_methods as $method => $pay): ?>
                        <div class="col-xxs-6 col-tn-6 col-xs-3 pay-method<?= $pay->isActive() ? '' : ' disabled' ?>">
                            <label class="label-method <?= $pay->isActive($this->amount) ? '' : 'label-disabled' ?>" for="<?= $method ?>-method">
                                <input name="method" id="<?= $method ?>-method"<?= $this->default_method == $method ? ' checked' : '' ?> <?= $pay->isActive($this->amount) ? '' : ' disabled="disabled"' ?> value="<?= $method ?>" type="radio">
                                <span class="method-text">
                                <?= $pay->getName() ?>
                                </span>
                                <img class="img-responsive img-method" alt="<?= $method ?>" title="<?= $pay->getDesc() ?>" src="<?= $pay->getIcon() ?>">
                            </label>
                        </div>
                    <?php endforeach ?>
					</div>

						<div class="form-group">
							<div class="col-md-10 col-md-offset-1 method-conditions">
								<div class="checkbox">
									<label>
										<input class="no-margin-checkbox" type="checkbox" name="anonymous" id="anonymous" value="1">
											<p class="label-checkbox">
                                            <?= $this->text('invest-anonymous') ?>
                                            </p>
									</label>
                                    
								</div>

                                <?php if(array_key_exists('pool', $this->pay_methods)): ?>
								<div class="checkbox">
									<label>
										<input class="no-margin-checkbox" type="checkbox" name="pool_on_fail" id="pool_on_fail" value="1">
											<p class="label-checkbox">
                                            <?= $this->text('invest-pool') ?><a data-toggle="modal" data-target="#myModal" href=""> <?= $this->text('invest-more-info') ?></a>
                                            </p>
									</label>
    							</div>
                                <?php endif ?>

                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-4 col-md-offset-1 invest-button">
                                <button type="submit" class="btn btn-block btn-success col-xs-3"><?= $this->text('invest-button') ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


	</div>
	<?= $this->insert('invest/partials/steps_bar') ?>

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

<?php $this->section('footer') ?>
<script type="text/javascript">
$(function(){

<?php if(array_key_exists('pool', $this->pay_methods)): ?>
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
</script>
<?php $this->append() ?>
