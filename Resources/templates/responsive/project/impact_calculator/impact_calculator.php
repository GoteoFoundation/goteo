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
        <div class="container">
            <img src="<?= $this->get_url() ?>/goteo_logo.png"  alt="huella">
            <h2>Lorem impsum dolor sit amet alliam est</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum mollis eros viverra tortor egestas, nec convallis est rhoncus. Etiam tempor lectus purus. Vivamos suscipit diam ut dui sagittis, vitae mollis quam sempre.</p>
        </div>

        <div class="container">
            <div class="row">
                <div class="footprint-details">
                    <div class="footprint-dropdown">
                        <div>
                            <h3>
                                <img width="75" src="<?= $this->asset('img/footprint/1.svg') ?>">
                                Huella Ecológica
                            </h3>
                            <p>
                                Lorem impsum dolor sit amet, consectetur adipiscing elit.
                            </p>
                        </div>
                        <button class="btn accordion-toggle" data-toggle="collapse" data-target="#collapse-footprint-1">
                            <span class="icon icon-arrow"></span>
                        </button>
                    </div>

                    <div id="collapse-footprint-1" class="collapse">
                        <?= $this->insert('project/impact_calculator/card') ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="footprint-details">
                    <div class="footprint-dropdown">
                        <div>
                            <h3>
                                <img width="75" src="<?= $this->asset('img/footprint/2.svg') ?>">
                                Huella Social
                            </h3>
                            <p>
                                Lorem impsum dolor sit amet, consectetur adipiscing elit.
                            </p>
                        </div>
                        <button class="btn accordion-toggle" data-toggle="collapse" data-target="#collapse-footprint-2">
                            <span class="icon icon-arrow"></span>
                        </button>
                    </div>

                    <div id="collapse-footprint-1" class="collapse">
                        <?= $this->insert('project/impact_calculator/card') ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="footprint-details">
                    <div class="footprint-dropdown">
                        <div>
                            <h3>
                                <img width="75" src="<?= $this->asset('img/footprint/3.svg') ?>">
                                Huella Democratica
                            </h3>
                            <p>
                                Lorem impsum dolor sit amet, consectetur adipiscing elit.
                            </p>
                        </div>
                        <button class="btn accordion-toggle" data-toggle="collapse" data-target="#collapse-footprint-3">
                            <span class="icon icon-arrow"></span>
                        </button>
                    </div>

                    <div id="collapse-footprint-3" class="collapse">
                        <?= $this->insert('project/impact_calculator/card') ?>
                    </div>
                </div>
            </div>
        </div>

        <button class="btn btn-large btn-cyan">GUARDAR Y CONTINUAR</button>
    </div>
</div>

<?php
    $this->append();
?>
