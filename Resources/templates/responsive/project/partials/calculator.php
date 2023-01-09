<?php
?>
<div class="col-md-4 col-xs-4">
    <h4>Calculadora</h4>
    <article class="card-impact-calculator">
        <div class="col-md-2 col-xs-2">
            <i class="icon icon-calculator"></i>
        </div>
        <div class="col-md-10 col-xs-10">
            <h3>Calcula el impacto de tu donación</h3>
            <p>Descubre el alcance de tu apoyo</p>
            <button class="btn" data-toggle="modal" data-target="#impact-calculator-modal">Haz tu cálculo aquí</button>
        </div>
    </article>
</div>

<div class="modal fade" id="impact-calculator-modal" tabindex="-1" role="dialog" aria-labelledby="admin-modal-label">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
        <label for="presupuestos">Cantidad de tu aportación</label>
        <div class="modal-input">
            <i class="icon icon-2x icon-money-bag"></i>
            <input type="text" name="presupuesto" width="100%" placeholder="100 €">
            <button type="submit" class="icon icon-2x icon-arrow"></button>
        </div>
        <p>Con tu donación contribuyes a poder hacer posible:</p>
      </div>
      <div class="modal-body">
        <h3>Creación de empleo</h3>
        <div class="row">
            <div class="col-md-4  col-sm-4 detail">
                <h4>Puestos de trabajo creado</h4>
                <p><i class="icon icon-2x icon-person"></i> 0,009</p>
            </div>
            <div class="col-md-4  col-sm-4">
                <h4>Impacto</h4>
                <p>Por cada 12,000€ se creará 1 puesto de trabajo</p>
            </div>
        </div>
        <h3>Personas formadas</h3>
        <div class="row">
            <div class="col-md-4 col-sm-4 detail">
                <h4>Número de personas</h4>
                <p><i class="icon icon-2x icon-medal"></i>0,1</p>
            </div>
            <div class="col-md-4 col-sm-4 impact">
                <h4>Impacto</h4>
                <p>1 persona formada por cada 937,50€</p>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <h2>De tu aportación de 100€ hacienda te devolverá 80€
      </div>
    </div>
  </div>
</div>
