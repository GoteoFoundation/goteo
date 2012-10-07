<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$project = $this['project'];
$contract = $this['contract'];

// si los datos ya han sido validados por el impulsor, no puede editarlos

$fields = array(
    
    // persona
    'name' => 'Nombre y apellidos (propios o representante)',
    'nif' => 'NIF (propio o representante)',
    'office' => 'Cargo (en la asociaci&oacute;n o entidad mercantil, solo si representa a una asociaci&oacute;n o entidad mercantil)',
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
    'reg_name' => 'Registro en el que se inscribi&oacute; la asociaci&oacute;n (si asociaci&oacute;n)<br />Nombre  del notario que  otorg&oacute; la escritura p&uacute;blica de la empresa (si entidad mercantil)',
    'reg_number' => 'N&uacute;mero de Registro (si asociaci&oacute;n)<br />N&uacute;mero del protocolo del notario (si entidad mercantil)',
    'reg_id' => 'Numero de inscripci&oacute;n en el Registro Mercantil (solo si entidad mercantil'
);


?>
<!--
<div class="widget projects">
    <h2 class="title">Acuerdo</h2>
</div>
-->

<div class="widget projects">
    <h2 class="title"><?php echo Text::get('contract-data_title') ?></h2>
    
<?php if (!empty($contract->status_pdf)) : ?>
    <p>Ya puedes descargarte el documento final del contrato, <a htref="#">CLICK AQUI</a>. Ya puedes imprimirlo, firmarlo y enviarnoslo a la siguiente direcci&oacute;n:</p>
    <p>Fundaci&oacute;n Fuentes Abiertas, direcci&oacute;n postal o apartado de correos</p>
<?php elseif ($contract->status_admin) : ?>
    <p>Ya hemos verificado los datos del contrato, pronto podr&aacute;s descarg&aacute;rtelo desde esta misma p&aacute;gina.</p>
    <p>A continuaci&oacute;n aparecen los datos, si hay alguna incorrecci&oacute;n ponte en contacto con nosotros enviando un email a <a href="mailto:info@goteo.org">info@goteo.org</a></p>
    <dl>
    <?php foreach ($fields as $field => $label) : if(empty($contract->$field)) continue; ?>
        <dt><?php echo $label ?></dt>
        <dd><?php echo $contract->$field; ?></dd>
    <?php endforeach; ?>
    </dl>
<?php elseif ($contract->status_owner) : ?>
    <p>Gracias por rellenar los datos, nos pondremos en contacto contigo para enviarte el contrato.</p>
    <p>A continuaci&oacute;n aparecen los datos facilitados, si hay alguna incorrecci&oacute;n o falta algo ponte en contacto con nosotros enviando un email a <a href="mailto:info@goteo.org">info@goteo.org</a></p>
    <dl>
    <?php foreach ($fields as $field => $label) : if(empty($contract->$field)) continue; ?>
        <dt><?php echo $label ?></dt>
        <dd><?php echo $contract->$field; ?></dd>
    <?php endforeach; ?>
    </dl>
<?php else : ?>
    <p>Pulsa APLICAR para guardar los cambios. Cuando todo est&eacute; listo, marca el recuadro "Dar por rellenados..." y pulsa APLICAR</p>
<form method="post" action="/dashboard/projects/contract/save" >
    <input type="hidden" name="id" value="<?php echo $contract->id; ?>" />
    <input type="hidden" name="project" value="<?php echo $project->id; ?>" />

    <p>Seleccionar una de las 3 opciones y rellenar datos seg&uacute;n opci&oacute;n elegida:</p>
    <ol>
        <li>
            <label>
                <input type="radio" name="type" value="0"<?php if ($contract->type == 0) echo ' checked="checked"'; ?>/> En su propio nombre y derecho
            </label>
        </li>
        <li>
            <label>
                <input type="radio" name="type" value="1"<?php if ($contract->type == 1) echo ' checked="checked"'; ?>/> Como representante de una asociaci&oacute;n
            </label>
        </li>
        <li>
            <label>
                <input type="radio" name="type" value="2"<?php if ($contract->type == 2) echo ' checked="checked"'; ?>/> Como apoderado de una entidad mercantil
            </label>
        </li>
    </ol>
    
    <?php foreach ($fields as $field => $label) : ?>
    <p>
        <label for="id-<?php echo $field?>"><?php echo $label ?></label><br />
        <input type="text" id="id-<?php echo $field?>" name="<?php echo $field?>" value="<?php echo $contract->$field; ?>" style="width:350px;" />
    </p>
    <?php endforeach; ?>


    <p>
        <label><input type="checkbox" id="close-data" name="close_owner" value="1" /> Dar por rellenados los datos</label>
    </p>

    <input type="submit" name="save" value="<?php echo Text::get('form-apply-button') ?>" />
</form>
<?php endif; ?>
</div>
