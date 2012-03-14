<?php

use Goteo\Library\Text;

$list = $this['list'];
$detail = $this['detail'];

$title = array(
    'receivers' => 'Destinatarios',
    'sended' => 'Enviados',
    'failed' => 'Falllidos',
    'pending' => 'Pendientes'
);
?>
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
