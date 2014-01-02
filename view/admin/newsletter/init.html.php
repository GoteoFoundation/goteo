<?php

use Goteo\Library\Text;

$mailing = $this['mailing'];

$link = SITE_URL.'/mail/'.base64_encode(md5(uniqid()).'¬any¬'.$mailing->mail).'/?email=any';

// mostrar enlace de si no ves y boton para activar
?>
<div class="widget">
    <p>La newsletter está lista para enviar con <a href="<?php echo $link; ?>" target="_blank">este contenido</a> a <?php echo $mailing->receivers ?> destinatarios.</p>
    <p>Si todo está bien pulsar el botón para activar los envíos automáticos.<br /> <a href="/admin/newsletter/activate/<?php echo $mailing->id; ?>" class="button" onclick="return confirm('Se comenzará a enviar automáticamente')">ACTIVAR!</a></p>

    <h3>Lista de destinatarios</h3>
    <table>
        <tr>
            <th>Email</th>
            <th>Alias</th>
            <th>Usuario</th>
        </tr>
        <?php foreach ($this['receivers'] as $user) : ?>
        <tr>
            <?php echo "<td>$user->email</td><td>$user->name</td><td>$user->user</td>" ?>
        </tr>
        <?php endforeach; ?>
    </table>
</div>