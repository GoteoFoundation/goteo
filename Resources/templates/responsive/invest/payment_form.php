<?php

$this->layout('invest/layout');

$this->section('main-content');

$pay_method = $this->pay_method;

?>

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


<?php $this->section('facebook-pixel') ?>
    <?= $this->insert('partials/facebook_pixel', ['pixel' => $this->project->facebook_pixel, 'track' => ['PageView', 'AddPaymentInfo']]) ?>
<?php $this->replace() ?>
