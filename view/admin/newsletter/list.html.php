<?php

use Goteo\Library\Text;

$mailing = $this['mailing'];

$link = SITE_URL.'/mail/'.base64_encode(md5(uniqid()).'¬any¬'.$mailing->mail).'/?email=any';

// si el mailing está desactivado , mostrar mensaje y botón para iniciar de nuevo
?>
<?php if (empty($mailing) || !$mailing->active) : ?>
<div class="widget board">
    <p>No se está enviando ningún boletín actualmente. Confirmar el asunto y pulsar el botón para generar uno nuevo con los datos actuales de plantilla y portada.</p>
    <form action="/admin/newsletter/init" method="post">
        <label>Asunto: <input type="text" name="subject" value="Newsletter Goteo" style="width:300px" /></label><br />
        <label>Es una prueba: <input type="checkbox" name="test" value="1" /></label><br />
        
        <input type="submit" name="init" value="Iniciar" />
    </form>
</div>
<?php endif; ?>
<?php if (!empty($mailing->id)) : ?>
<div class="widget board">
        <p>
           Asunto: <strong><?php echo $mailing->subject ?></strong><br />
           Iniciado el: <strong><?php echo $mailing->date ?></strong><br />
           Estado del envío automático: <?php echo ($mailing->active) 
               ? '<span style="color:green;font-weight:bold;">Activo</span>'
               : '<span style="color:red;font-weight:bold;">Inactivo</span>' ?>
        </p>

    <table>
        <thead>
            <tr>
                <th><!-- Si no ves --></th>
                <th>Fecha</th>
                <th><a href="/admin/newsletter/detail/receivers" title="Ver todos los destinatarios">Destinatarios</a></th>
                <th><a href="/admin/newsletter/detail/sended" title="Ver todos los enviados">Enviados</a></th>
                <th><a href="/admin/newsletter/detail/failed" title="Ver todos los fallidos">Fallidos</a></th>
                <th><a href="/admin/newsletter/detail/pending" title="Ver todos los pendientes">Pendientes</a></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="<?php echo $link; ?>" target="_blank">[Si no ves]</a></td>
                <td><?php echo $mailing->date; ?></td>
                <td style="width:15%"><?php echo $mailing->receivers; ?></td>
                <td style="width:15%"><?php echo $mailing->sended; ?></td>
                <td style="width:15%"><?php echo $mailing->failed; ?></td>
                <td style="width:15%"><?php echo $mailing->pending; ?></td>
            </tr>
        </tbody>
    </table>
</div>
<?php else : ?>
<p>No hay ningún envío de newsletter registrado, ni activo ni inactivo</p>
<?php endif; ?>