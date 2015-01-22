<?php

use Goteo\Library\Text;

$data = $this['data'];
?>
<div class="widget board">
    <p>Usuarios registrados: <strong><?php echo $data['registered']; ?></strong></p>
    <p>Usuarios sin campo localidad rellenado: <strong><?php echo $data['no-location']; ?></strong></p>
    <p>Usuarios geolocalizados: <strong><?php echo $data['located']; ?></strong></p>
    <p>Usuarios no geolocalizados: <strong><?php echo $data['unlocated']; ?></strong></p>
    <p>Usuarios ilocalizables: <strong><?php echo $data['unlocable']; ?></strong> (Geolocalización expresamente denegada por el usuario)</p>
    <p>Usuarios localizados fuera de espa&ntilde;a: <strong><?php echo $data['not-spain']; ?></strong></p>

    <p><strong>Asignados a nodo:</strong><br />
        <table>
            <?php foreach ($data['by-node'] as $nodeName=>$nodeCount) : ?>
            <tr>
                <td><?php echo $nodeName ?></td>
                <td><?php echo $nodeCount ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </p>

    <p><strong>Por regiones espa&ntilde;olas:</strong><br />
        <table>
            <?php foreach ($data['by-region'] as $regionName=>$regionCount) : ?>
            <tr>
                <td><?php echo $regionName ?></td>
                <td><?php echo $regionCount ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </p>

    <p><strong>Por paises:</strong><br />
        <table>
            <?php foreach ($data['by-country'] as $countryName=>$countryCount) : ?>
            <tr>
                <td><?php echo $countryName ?></td>
                <td><?php echo $countryCount ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </p>
</div>
