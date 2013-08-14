<?php
use Goteo\Library\Text;

$contract = $this['contract'];

// montar visualización de campos segun tipo

$fields = array(
    
    // persona
    'name' => 'Nombre y apellidos (propios o representante)',
    'nif' => 'NIF (propio o representante)',
    'office' => 'Cargo en la asociaci&oacute;n o entidad mercantil<br /> (solo si representa a una asociaci&oacute;n o entidad mercantil)',
    'address' => 'Domicilio (propio o representante)',
    'location' => 'Municipio (propio o representante)',
    'region' => 'Provincia (propio o representante)',
    'zipcode' => 'Código postal (propio o representante)',
    'country' => 'Pa&iacute;s (propio o representante)',
    
    // entidad
    'entity_name' => 'Nombre o raz&oacute;n social (de la asociaci&oacute;n o entidad mercantil)',
    'entity_cif' => 'CIF (de la asociaci&oacute;n o entidad mercantil)',
    'entity_address' => 'Domicilio social (de la asociaci&oacute;n o entidad mercantil)',
    'entity_location' => 'Municipio (de la asociaci&oacute;n o entidad mercantil)',
    'entity_region' => 'Provincia (de la asociaci&oacute;n o entidad mercantil)',
    'entity_zipcode' => 'Código postal (de la asociaci&oacute;n o entidad mercantil)',
    'entity_country' => 'Pa&iacute;s (de la asociaci&oacute;n o entidad mercantil)',
    
    // registro
    'reg_name' => 'Registro en el que se inscribi&oacute; la asociaci&oacute;n (si asociaci&oacute;n)<br /> / Nombre  del notario que  otorg&oacute; la escritura p&uacute;blica de la empresa (si entidad mercantil)',
    'reg_date' => 'Fecha en que se otorg&oacute; la escritura p&uacute;blica de la empresa (solo si entidad mercantil)',
    'reg_number' => 'N&uacute;mero de Registro (si asociaci&oacute;n)<br /> / N&uacute;mero del protocolo del notario (si entidad mercantil)',
    'reg_id' => 'Numero de inscripci&oacute;n en el Registro Mercantil (solo si entidad mercantil)'
);


?>
<div class="widget">
    <h2 class="title"><?php echo Text::get('contract-data_title') ?></h2>
    
    <?php foreach ($fields as $field => $label) : ?>
    <dl>
        <dt><?php echo $label ?></dt>
        <dd><?php echo $contract->$field; ?></dd>
    </dl>
    <br />
    <?php endforeach; ?>
</div>

<div class="widget">
    <h2 class="title" style="color: red;">NOTA IMPORTANTE:</h2>

<p>Si sois entidades jurídicas de cualquier tipo, la recaudación tributará en el Impuesto de Sociedades. En el caso de entidades sin ánimo de lucro, para saber si estáis exentas, deberíais consultar a vuestro asesor fiscal porque depende de cada caso.</p>
<p>En el caso de personas físicas, dependerá del objeto del proyecto para el que se dona el dinero, y pueden darse diferentes situaciones:</p>
<p>a) que no estéis sujetos/as al Impuesto sobre Sucesiones y Donaciones en aplicación del ar. 3 del Reglamento del Impuesto que, entre otras, declara no sujetas “las subvenciones, becas, premios, primas, gratificaciones y auxilios que se concedan por Entidades públicas o privadas con fines benéficos, docentes, culturales, deportivos o de acción social”, como es el caso de la Fundación Fuentes Abiertas, entidad que gestiona Goteo.org.</p>
<p>En este caso el dinero recaudado tributará en el IRPF dependiendo esta tributación del concepto en el que sea declarado: si es una actividad económica de la que se esté dado de alta se podrán deducir los gastos asociados al proyecto, y si no es así, será una ganancia patrimonial neta, sin poderse deducir nada.</p>
<p>b) Que estéis sujetos al Impuesto sobre Sucesiones y Donativos por no cumplirse lo anterior.</p>

    
</div>
