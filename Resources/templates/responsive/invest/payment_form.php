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

<?= $this->insert('invest/partials/steps_bar') ?>

<div class="container">

	<div class="row row-form">
			<div class="panel panel-default invest-container">
				<div class="panel-body">
					<h2 class="col-md-offset-1 padding-bottom-2"><?= $this->text('invest-method-title') ?></h2>

					<div class="col-md-10 col-md-offset-1 reminder">
                        <div class="level-1">
					       <?= $this->text('invest-alert-investing') ?><span class="amount-reminder"><?= \amount_format($this->amount) ?></span> <?= $this->text('invest-project') ?> <span class="uppercase"><?= $this->project->name ?></span>
						</div>
                        <?php if($this->reward): ?>
						<div class="level-2">
							<?= $this->text('invest-alert-rewards') ?>
	                        <strong class="text-uppercase"><?= $this->reward->reward ?></strong>
						</div>
					<?php endif; ?>
					</div>

                    <?= $this->supply('sub-header', $this->get_session('sub-header')) ?>


					<form class="form-horizontal" role="form" method="POST" action="">
                    <!-- TODO: hidden clases -->
                    <!-- RETURN URL /invest/<?= $this->project->id ?>/12124 -->

                    <div class="row no-padding col-md-10 col-md-offset-1">
                        <div class="col-xxs-6 col-tn col-xs-3 pay-method<?= $pay_method->isActive() ? '' : ' disabled' ?>">
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

</div>

<?php $this->replace() ?>

<?php //Add facebook pixel to track Facebook ads ?>
<?php if($this->project->facebook_pixel): ?>

<?php $this->section('footer') ?>
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?= $this->ee($this->project->facebook_pixel, "js") ?>');
fbq('track', 'PageView');
fbq('track', 'AddPaymentInfo');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?= $this->ee($this->project->facebook_pixel, "js") ?>&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->
<?php $this->append() ?>

<?php endif; ?>
