<?php

$this->layout('invest/layout', ['alt_title' => $this->text('invest-make-sure-title')]);

$this->section('main-content');

?>

<div class="container">

    <div class="row row-form">
        <div class="panel panel-default invest-container">
            <div class="panel-body">

                <h2 class="col-sm-offset-1 col-sm-10 padding-bottom-2"><?= $this->text('invest-make-sure-title') ?></h2>

                <?= $this->insert('invest/partials/invest_header_form') ?>

                <form class="form col-sm-offset-1 col-sm-10" id="make-sure-form" role="form" method="POST" action="/invest/<?= $this->project->id ?>/<?= $this->invest->id ?>">

                    <?= $this->supply('invest-form', $this->insert('invest/partials/invest_address_form')) ?>

                    <?= $this->insert('invest/partials/invest_submit_form') ?>

                </form>

            </div>
        </div>
    </div>

</div>

<?php $this->replace() ?>

<?php $this->section('facebook-pixel') ?>
    <?= $this->insert('partials/facebook_pixel', [
        'pixel' => $this->project->facebook_pixel,
        'track' => ['PageView', 'Purchase' => ['value' => $this->invest->amount, 'currency' => 'EUR']]
    ]) ?>
<?php $this->replace() ?>
