<?php


$this->layout('questionnaire/layout', [
  'meta_title' => $this->t('questionnaire').' :: '. $this->model->name,
  'meta_description' => $this->model->name .'. '.$this->model->description
  ]);

$this->section('content');

$checked = $this->has_query('select');
?>

<div class="container create-form apply" >
    <div class="channel-info">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <h2 class="text-center channel-title"><?= $this->text('questionnaire-apply-title', $this->model->name) ?></h1>
            </div>
        </div>

        
        <div class="row">
        <?php if ($this->model->logo): ?>
            <div class="col-md-2 col-sm-2 ">
                <img class="logo" src="<?= $this->model->logo->getLink(150,0) ?>">
            </div>
            <div class="col-md-8 col-sm-8 description">
               <p> <?= $this->model->description; ?></p>
            </div>
        <?php else: ?>
            <div class="col-md-8 col-sm-8 col-sm-offset-2 col-md-offset-2 description">
               <p> <?= $this->model->description; ?></p>
            </div>
        <?php endif; ?>
        </div>
    </div>
    <div class="row spacer-20">
        <div class="col-md-12">
            <div class="text-cyan">
                <p><?= $this->text('questionnaire-answer-questions') ?></p>
            </div>
        </div>
    </div>

    <?= $this->form_form($this->raw('form')) ?>

</div>

<!-- End container fluid -->


<?php $this->append() ?>

<?php $this->section('footer') ?>

<link rel="stylesheet" href="<?= SRC_URL ?>/assets/vendor/dropzone/dist/dropzone.css" type="text/css">

<?= $this->insert('admin/partials/javascript_editors') ?>

<?php $this->append() ?>