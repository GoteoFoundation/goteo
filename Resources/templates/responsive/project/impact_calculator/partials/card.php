<?php
?>

<article class="card <?= $this->active ? "active footprint-$this->footprint": ''?>">

    <div class="info">
        <i class="fa fa-info"
           data-html="true"
           data-container="body"
           data-toggle="tooltip"
           title=""
           data-original-title="
                <span >
                    <h4 style='font-size: 16px; font-weight:bold;'>CREACIÓN DE EMPLEO</h4>
                    <p>Según el Instituto Nacional de Estadística (INE) el coste medio de un trabajador a jornada completa en España es de 31.150€/año.</`>
                </span>">
        </i>
    </div>

    <label for="presupuestos">Presupuesto</label>
    <div class="card-input">
        <i class="icon icon-money-bag"></i>
        <input type="text" name="presupuesto">
    </div>

    <label for="number_personas">Número de personas</label>
    <div class="card-input">
        <i class="icon icon-medal"></i>
        <input type="text" name="number_personas">
    </div>

    <div>
        <h4>Impacto</h4>
        <div class="card-input row">
            <div class="col-xs-4">
            <i class="icon icon-classroom"></i></div>
            <div class="col-xs-8"><p>Por cada 12.000€ se creará 1 puesto de trabajo</p></div>
        </div>
    </div>
</article>

