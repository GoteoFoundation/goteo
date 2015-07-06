<?php

$this->layout( $this->is_ajax() ? 'wrapper' : 'layout', [
    'bodyClass' => 'project-edit',
    'superform' => true
]);


$contract = $this->contract;
$step = $this->step;
$steps = $this->steps;

?>


<?php $this->section('sub-header') ?>
    <div id="sub-header">
        <div class="project-header">
            <h2><span><?= $contract->project_name ?></span></h2>
        </div>
    </div>
<?php $this->replace() ?>


<?php $this->section('content') ?>
    <div id="main" class="<?= $step ?>">

        <form method="post" action="<?= '/contract/edit/' . $contract->project .($step ? '/' . $step : '') ?>" class="project" enctype="multipart/form-data" >

            <?= $this->insert('contract/partials/edit_steps', ['steps' => $steps, 'step' => $step, 'errors' => $contract->errors]) ?>

            <?= $this->supply('contract-edit-step') ?>

            <?= $this->insert('contract/partials/edit_steps', ['steps' => $steps, 'step' => $step, 'errors' => $contract->errors]) ?>

        </form>

    </div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>
    <script type="text/javascript">
    $(function () {
        $('div.superform').bind('superform.ajax.done', function (event, html, new_el) {
            $('#li-errors').superform(html);
        });
    });
    </script>
<?php $this->append() ?>
