<?php

use Goteo\Library\Text;
use Goteo\Library\Mail;

$mailing = $vars['mailing'];
$list = $vars['list'];
$detail = $vars['detail'];

$link = Mail::getSinovesLink($mailing->mail);

$title = array(
    'receivers' => 'Destinatarios',
    'sended' => 'Enviados',
    'failed' => 'Falllidos',
    'pending' => 'Pendientes'
);
?>
<?php if (!empty($mailing)) : ?>
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
                <th><a href="/admin/newsletter/detail/<?php echo $mailing->id; ?>?show=receivers" title="Ver destinatarios">Destinatarios</a></th>
                <th><a href="/admin/newsletter/detail/<?php echo $mailing->id; ?>?show=sended" title="Ver enviados">Enviados</a></th>
                <th><a href="/admin/newsletter/detail/<?php echo $mailing->id; ?>?show=failed" title="Ver fallidos">Fallidos</a></th>
                <th><a href="/admin/newsletter/detail/<?php echo $mailing->id; ?>?show=pending" title="Ver pendientes">Pendientes</a></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php if (!empty($link)) : ?><a href="<?php echo $link; ?>" target="_blank">[Si no ves]</a><?php endif; ?></td>
                <td><?php echo $mailing->date; ?></td>
                <td style="width:15%"><?php echo $mailing->receivers; ?></td>
                <td style="width:15%"><?php echo $mailing->sended; ?></td>
                <td style="width:15%"><?php echo $mailing->failed; ?></td>
                <td style="width:15%"><?php echo $mailing->pending; ?></td>
            </tr>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($detail)) : ?>
<h3>Viendo el listado completo de los <?php echo $title[$detail] ?></h3>
<div class="widget board">
    <table>
        <tr>
            <th>Email</th>
            <th>Alias</th>
            <th>Usuario</th>
        </tr>
        <?php foreach ($list as $user) : ?>
        <tr>
            <?php echo "<td>$user->email</td><td>$user->name</td><td>$user->user</td>" ?>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>
