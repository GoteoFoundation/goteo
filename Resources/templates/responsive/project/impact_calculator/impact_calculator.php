<?php
    $this->layout('layout', [
        'bodyClass' => 'project impact-calculator',
        'title' => $this->text('meta-title-create-project'),
        'meta_description' => $this->text('meta-description-create-project')
        ]);

    $this->section('content');
    $footprints = $this->footprints;
?>
<div class="impact-calculator-container">
    <div class="impact-calculator-header container">
        <h1><?= $this->t('project-impact-calculator-title')?></h1>
        <p><?= $this->t('project-impact-calculator-description') ?></p>
    </div>

    <div class="impact-calculator-body">
        <div class="container impact-calculator-body-title">
            <img src="<?= $this->asset('img/impact-calculator/fingerprint.svg') ?>"  alt="<?= $this->t('regular-footprints') ?>">
            <h2><?= $this->t('project-impact-calculator-body-title') ?></h2>
            <p><?= $this->t('project-impact-calculator-body-description') ?></p>
        </div>

        <form
            action=""
            method="POST"
            name="form"
            enctype="multipart/form-data">
            <div class="container">
                <div class="row">
                        <?php foreach($footprints as $footprint):?>
                            <?= $this->insert('project/impact_calculator/partials/footprint_details', ['footprint' => $footprint]) ?>
                        <?php endforeach; ?>
                </div>

                <div class="row spacer-20 spacer-bottom-20">
                    <div class="impact-calculator-save col-md-4 col-md-offset-1">
                        <button type="submit" class="btn btn-lg btn-cyan btn-block"><?= $this->t('regular-save') ?></button>
                    </div>

                    <div class="impact-calculator-save col-md-4 col-md-offset-1">
                        <a class="btn btn-lg btn-cyan btn-block" href="/dashboard/project/<?= $this->project->id?>"><?= $this->t('regular-continue-without-saving') ?></a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
    $this->append();

    $this->section('footer');
?>

<!-- POST PROCESSING THIS JAVASCRIPT BY GRUNT -->
<!-- build:js assets/js/impact-calculator.js -->
    <script type="text/javascript" src="<?= $this->asset('js/impact_calculator/impact_calculator.js') ?>"></script>
<!-- endbuild -->

<?php
    $this->append();
?>
