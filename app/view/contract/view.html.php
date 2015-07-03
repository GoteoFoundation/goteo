<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$contract = $vars['contract'];

$bodyClass = 'contract';


// temporal, luego esto mostrará el pdf dinámico (o estático si ya se ha grabado)
// mientras sea generado (y no grabado) el pdf se mostrará con un sello [Invalid]

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





include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';

echo View::get('contract/widget/review.html.php', $vars);

include __DIR__ . '/../footer.html.php';
include __DIR__ . '/../epilogue.html.php';
?>
