<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$project = $this['project'];
$contract = $this['contract'];
$status = $this['status']; // datos de estado de contrato



/*
 * Segun el estado de proceso:
 * 
 * Mensaje que dice en que estado se encuentra y que debería hacer a continuación
 * 
 * botón para dar por cerrados los datos 
 * botón para informar que ha actualizado las cuentas
 * 
 * enlace a la edición de contrato
 * enlace al pdf
 * 
 */



?>
<div class="widget projects">
    <h2 class="title"><?php echo Text::get('contract-data_title') ?></h2>
    Aquí funcionalidades y mensajes para el proceso de contrato, informe y pago.
</div>

<div class="widget projects">
    <h2 class="title">Formulario de Contrato</h2>
    
    <p>- Datos personales del promotor del proyecto<br />
        - Cuentas bancarias del impulsor<br />
        - Otros datos legales.
        
        <a htref="/contract/edit/<?php echo $project->id ?>">Editar</a>
    </p>
    
</div>

<div class="widget projects">
    <h2 class="title">Datos de Contrato</h2>
    
    <p>La edición de datos está cerrada, a continuación un listado de los datos introducidos.</p>
    <p>Si hay alguna incorrecci&oacute;n ponte en contacto con nosotros enviando un email a <a href="mailto:info@goteo.org">info@goteo.org</a></p>
    <dl>
    <?php foreach ($fields as $field => $label) : if(empty($contract->$field)) continue; ?>
        <dt><?php echo $label ?></dt>
        <dd><?php echo $contract->$field; ?></dd>
    <?php endforeach; ?>
    </dl>
    
</div>

<div class="widget projects">
    <h2 class="title">Formulario de Contrato</h2>
    
    <p>Pdf del texto íntegro del contrato
        
        <a htref="/contract/<?php echo $project->id ?>">Descargar</a>
    </p>
    
</div>

<div class="widget projects">
    <h2 class="title">Informe financiero</h2>
    
    <p>Este es el informe final de financiación en goteo</p>
    
</div>
