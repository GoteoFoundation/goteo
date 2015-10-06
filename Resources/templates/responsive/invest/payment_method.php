<?php

$this->layout('layout', [
    'bodyClass' => '',
    'title' => 'Realizar aporte :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

?>
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
                        <div class="col-xs-6 col-md-3 pay-method<?= $pay->isActive() ? '' : ' disabled' ?>">
                            <label class="label-method" for="<?= $method ?>-method">
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
										<input class="confirm-checkbox" type="checkbox" name="remember">
											<?= $this->text('invest-anonymous') ?>
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input class="confirm-checkbox" type="checkbox" name="remember">
											<?= $this->text('invest-pool') ?>
									</label>

								</div>
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
</div>

<?php $this->replace() ?>
