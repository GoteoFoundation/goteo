<?php

$this->layout('layout', [
    'bodyClass' => '',
    'title' => 'Realizar aporte :: Goteo.org',
    'meta_description' => $this->text('meta-description-discover')
    ]);

$this->section('content');

$pay_method = $this->pay_method;

?>

<?= $this->insert('invest/partials/project_info') ?>

<div class="container">

	<div class="row row-form">
			<div class="panel panel-default select-method">
				<div class="panel-body">
					<h2 class="col-md-offset-1 padding-bottom-2"><?= $this->text('invest-method-title') ?></h2>

					<div class="col-md-10 col-md-offset-1 reminder">
					<?= $this->text('invest-alert-investing') ?><span class="amount-reminder"><?= amount_format($this->amount) ?></span> <?= $this->text('invest-project') ?> <span class="uppercase"><?= $this->project->name ?></span>
						<?php if($this->reward): ?>
						<div>
							<?= $this->text('invest-alert-rewards') ?>
	                        <strong style="text-transform: uppercase;"><?= $this->reward->reward ?></strong>
						</div>
					<?php endif; ?>
					</div>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>


					<form class="form-horizontal" role="form" method="POST" action="">
                    <!-- TODO: hidden clases -->
                    <!-- RETURN URL /invest/<?= $this->project->id ?>/12124 -->

                    <div class="row no-padding col-md-10 col-md-offset-1">
                        <div class="col-xs-6 col-md-3 pay-method<?= $pay_method->isActive() ? '' : ' disabled' ?>">
                            <div class="label-method">
                                <span class="method-text">
                                <?= $pay_method->getName() ?>
                                </span>
                                <img class="img-responsive img-method" alt="<?= $pay_method::getId() ?>" title="<?= $pay_method->getDesc() ?>" src="<?= $pay_method->getIcon() ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-4 col-md-offset-1 invest-button">
                            <button type="submit" class="btn btn-block btn-success col-xs-3"><?= $this->text('invest-do-payment') ?></button>
                        </div>
                    </div>

                	</form>
				</div>
			</div>
	</div>
    <?= $this->insert('invest/partials/steps_bar') ?>
</div>

<?php $this->replace() ?>
