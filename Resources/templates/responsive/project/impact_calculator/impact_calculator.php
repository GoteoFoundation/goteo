<?php
    $this->layout('layout', [
        'bodyClass' => 'project impact-calculator',
        'title' => $this->text('meta-title-create-project'),
        'meta_description' => $this->text('meta-description-create-project')
        ]);

    $this->section('content');
?>
<div class="impact-calculator-container">
    <div class="impact-calculator-header container">
        <h1>Compromiso de impacto</h1>
        <p>Calcula el impacto de tu campaña a través de huellas e indicadores</p>
    </div>

    <div class="impact-calculator-body">
        <div class="container impact-calculator-body-title">
            <img src="<?= $this->asset('img/impact-calculator/fingerprint.svg') ?>"  alt="huella">
            <h2>Lorem impsum dolor sit amet alliam est</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum mollis eros viverra tortor egestas, nec convallis est rhoncus. Etiam tempor lectus purus. Vivamos suscipit diam ut dui sagittis, vitae mollis quam sempre.</p>
        </div>

        <div class="container">
            <div class="row">
                <?= $this->insert('project/impact_calculator/partials/footprint_details', [ 'footprint' => 1]) ?>
            </div>
            <div class="row">
                <?= $this->insert('project/impact_calculator/partials/footprint_details', [ 'footprint' => 2]) ?>
            </div>
            <div class="row">
                <?= $this->insert('project/impact_calculator/partials/footprint_details', [ 'footprint' => 3]) ?>
            </div>
        </div>

        <div class="impact-calculator-save">
            <button class="btn btn-large btn-cyan">GUARDAR Y CONTINUAR</button>
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
