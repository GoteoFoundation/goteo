<?php
use Goteo\Library\Text;

$contract = $vars['contract'];

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
    'reg_name' => 'Registro en el que se inscribi&oacute; la entidad/asociaci&oacute;n',
    'reg_date' => 'Fecha en que se otorg&oacute; la escritura p&uacute;blica de la empresa (solo si entidad mercantil)',
    'reg_number' => 'N&uacute;mero de Registro (si asociaci&oacute;n o entidad mercantil)',
    'reg_id' => 'N&uacute;mero del protocolo del notario (solo si entidad mercantil)',
    'reg_idname' => 'Nombre  del notario que  otorg&oacute; la escritura p&uacute;blica de la empresa (si entidad mercantil)',
    'reg_idloc' => 'Ciudad de actuaci&oacute;n del notario (solo si entidad mercantil)',

    // cuentas
    'paypal' => 'Cuenta PayPal del proyecto',
    'paypal_owner' => 'Titular de la cuenta PayPal',
    'bank' => 'Cuenta bancaria del proyeccto',
    'bank_owner' => 'Titular de la cuenta bancaria'

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
