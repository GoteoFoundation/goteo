<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php

$mailing = $this->mailing;
$list = $this->list;
$detail = $this->detail;

$title = array(
    'receivers' => 'Destinatarios',
    'sent' => 'Enviados',
    'failed' => 'Falllidos',
    'pending' => 'Pendientes'
);
?>
<?php if ($mailing) : ?>
<div class="widget board">
        <p>
           Asunto: <strong><?= $mailing->subject ?></strong><br />
           Iniciado el: <strong><?= $mailing->date ?></strong><br />
           Estado del envío automático: <?= ($mailing->active)
               ? '<span style="color:green;font-weight:bold;">Activo</span>'
               : '<span style="color:red;font-weight:bold;">Inactivo</span>' ?>
        </p>

    <table>
        <thead>
            <tr>
                <th><!-- Si no ves --></th>
                <th>Fecha</th>
                <th><a href="/admin/newsletter/detail/<?= $mailing->id ?>?show=receivers" title="Ver destinatarios">Destinatarios</a></th>
                <th><a href="/admin/newsletter/detail/<?= $mailing->id ?>?show=sent" title="Ver enviados">Enviados</a></th>
                <th><a href="/admin/newsletter/detail/<?= $mailing->id ?>?show=failed" title="Ver fallidos">Fallidos</a></th>
                <th><a href="/admin/newsletter/detail/<?= $mailing->id ?>?show=pending" title="Ver pendientes">Pendientes</a></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="<?= $this->link ?>" target="_blank">[Visualizar]</a></td>
                <td><?= $mailing->date ?></td>
                <td style="width:15%"><?= $mailing->receivers ?></td>
                <td style="width:15%"><?= $mailing->sent ?></td>
                <td style="width:15%"><?= $mailing->failed ?></td>
                <td style="width:15%"><?= $mailing->pending ?></td>
            </tr>
        </tbody>
    </table>
</div>
<?php endif ?>

<?php if ($detail) : ?>
<h3>Viendo el listado completo de los <?= $title[$detail] ?></h3>
<div class="widget board">
    <table>
        <tr>
            <th>Email</th>
            <th>Alias</th>
            <th>Usuario</th>
        </tr>
        <?php foreach ($list as $user) : ?>
        <tr>
            <?= "<td>{$user->email}</td><td>{$user->name}</td><td>{$user->user}</td>" ?>
        </tr>
        <?php endforeach ?>
    </table>
</div>
<?php endif ?>



<?php $this->replace() ?>
