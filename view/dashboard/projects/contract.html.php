<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$project = $this['project'];
$contract = $this['contract'];
$status = $this['status']; // datos de estado de contrato

// campos
$fields = array(
    
    // persona
    'name' => 'Nombre y apellidos (propios o representante)',
    'nif' => 'NIF (propio o representante)',
    'office' => 'Cargo en la asociaci&oacute;n o entidad mercantil<br /> (solo si representa a una asociaci&oacute;n o entidad mercantil)',
    'address' => 'Domicilio (propio o representante)',
    'location' => 'Municipio (propio o representante)',
    'region' => 'Provincia (propio o representante)',
    'country' => 'Pa&iacute;s (propio o representante)',
    
    // entidad
    'entity_name' => 'Nombre o raz&oacute;n social (de la asociaci&oacute;n o entidad mercantil)',
    'entity_cif' => 'CIF (de la asociaci&oacute;n o entidad mercantil)',
    'entity_address' => 'Domicilio social (de la asociaci&oacute;n o entidad mercantil)',
    'entity_location' => 'Municipio (de la asociaci&oacute;n o entidad mercantil)',
    'entity_region' => 'Provincia (de la asociaci&oacute;n o entidad mercantil)',
    'entity_country' => 'Pa&iacute;s (de la asociaci&oacute;n o entidad mercantil)',
    
    // registro
    'reg_name' => 'Registro en el que se inscribi&oacute; la asociaci&oacute;n (si asociaci&oacute;n)<br /> / Nombre  del notario que  otorg&oacute; la escritura p&uacute;blica de la empresa (si entidad mercantil)',
    'reg_number' => 'N&uacute;mero de Registro (si asociaci&oacute;n)<br /> / N&uacute;mero del protocolo del notario (si entidad mercantil)',
    'reg_id' => 'Numero de inscripci&oacute;n en el Registro Mercantil (solo si entidad mercantil)'
);

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
    
<form method="post" action="/dashboard/projects/contract/save" >
    <input type="hidden" name="id" value="<?php echo $contract->id; ?>" />
    <input type="hidden" name="project" value="<?php echo $project->id; ?>" />
    
    <p>
        <label><input type="checkbox" id="close-data" name="close_owner" value="1" /> Dar por rellenados los datos</label>
    </p>

    <input type="submit" name="save" value="<?php echo Text::get('form-apply-button') ?>" />
</form>
    
    
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
