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

        <div class="container">
            <div class="row">
                <?php foreach ($footprints as $footprint): ?>
                    <?= $this->insert('project/impact_calculator/partials/footprint_details', ['footprint' => $footprint]) ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="impact-calculator-save">
            <button class="btn btn-large btn-cyan"><?= $this->t('regular-save') ?></button>
        </div>
    </div>
</div>

<?php
    $this->append();

    $this->section('footer');

?>

<script type="application/javascript">
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<?php
    $this->append();
?>
