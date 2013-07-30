<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$project = $this['project'];
$contract = $this['contract'];
$status = $this['status']; // datos de estado de contrato

if ($project->status < 3) return '';

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
    <?php echo \trace($contract->status); ?>
    
<?php if (!$contract->status->owner) : ?>
    <p>En el Formulario de contrato puedes modificar:<br />
        - Datos personales del promotor del proyecto<br />
        - Cuentas bancarias del impulsor<br />
        - Otros datos legales.<br /><br />
        
        <a href="/contract/edit/<?php echo $project->id ?>" target="_blank" class="button">Editar</a>
    </p>

    <hr />
    
    <form method="post" action="/dashboard/projects/contract" >
        <input type="submit" name="close_owner" value="Doy por rellenados los datos, no necesito editarlos mas" class="weak" />
    <br />
        <input type="submit" name="account_update" value="Informar al admin de que he modificado las cuentas bancarias" />
    </form>
    
<?php else: ?>
    <p>
        La edición de datos está cerrada, a continuación un listado de los datos introducidos.<br />
        Si hay alguna incorrecci&oacute;n ponte en contacto con nosotros enviando un email a <a href="mailto:info@goteo.org">info@goteo.org</a>
    </p>
    
    <dl>
    <?php foreach ($fields as $field => $label) : if(empty($contract->$field)) continue; ?>
        <dt><?php echo $label ?></dt>
        <dd><?php echo $contract->$field; ?></dd>
    <?php endforeach; ?>
    </dl>
<?php endif; ?>
    
</div>


<?php if ($contract->status->owner) : ?>
<div class="widget projects">
    <h2 class="title">Pdf contrato</h2>
<?php if ($contract->status->admin) : ?>
    <p>Puedes consultar el contenido provisional del contrato, aun esta en revision.<br />
        <a href="/contract/<?php echo $project->id ?>" target="_blank" class="button">Consultar</a>
    </p>
<?php else : ?>
    <p>Ya puedes descargar el pdf del contrato. Fírmalo y nos o envias a ....<br />
        <a htref="/contract/<?php echo $project->id ?>" target="_blank" class="button">Descargar</a>
    </p>
<?php endif; ?>
</div>
<?php endif; ?>

<?php /* if ($contract->status->report) : ?>
<div class="widget projects">
    <h2 class="title">Informe financiero</h2>
    
    <p>Este es el informe final de financiación en goteo</p>
    <?php echo new View('view/project/report.html.php', array('project'=>$project, 'Data'=>$this['Data'])); ?>
</div>
<?php endif; */ ?>
