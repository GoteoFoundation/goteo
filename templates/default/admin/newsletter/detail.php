<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php

$mailing = $this->mailing;
$status = $mailing->getStatus();
$list = $this->list;
$detail = $this->detail;

$title = array(
    'receivers' => 'Destinatarios',
    'sent' => 'Enviados',
    'failed' => 'Falllidos',
    'pending' => 'Pendientes'
);
?>
<a href="/admin/newsletter" class="button">Volver</a>
<?php if(!$mailing->active): ?>
<a href="/admin/newsletter/activate/<?= $mailing->id ?>" class="button">Enviar el boletin ahora</a>
<?php endif ?>
<a href="/admin/newsletter/cancel/<?= $mailing->id ?>" onclick="return confirm('El boletín se borrará, seguro que quieres continuar?')" class="button">Cancelar y eliminar este boletin</a>

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
                <th>Percent</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="<?= $this->link ?>" target="_blank">[Visualizar]</a></td>
                <td><?= $mailing->date ?></td>
                <td style="width:12%"><?= $status->receivers ?></td>
                <td style="width:12%"><?= $status->sent ?></td>
                <td style="width:12%"><?= $status->failed ?></td>
                <td style="width:12%"><?= $status->pending ?></td>
                <td style="width:12%"><?= number_format($status->percent, 2, ',', '') ?>%</td>
            </tr>
        </tbody>
    </table>
</div>
<?php endif ?>

<h3>Viendo el listado completo de los <?= $title[$detail] ?></h3>
<?php if ($detail) : ?>
    <div class="widget board">
    <table>
        <tr>
            <th>Email</th>
            <th>Alias</th>
            <th>Usuario</th>
            <th>Estado</th>
        </tr>
        <?php foreach ($list as $user) : ?>
        <tr>
            <td><?= $user->email ?></td>
            <td><?= $user->name ?></td>
            <td><?= $user->user ?></td>
            <td><?= $user->status . ($user->error ? $user->error : '')  ?></td>
        </tr>
        <?php endforeach ?>
    </table>
    </div>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit]) ?>

<?php else : ?>
    <p>No se han encontrado registros</p>
<?php endif ?>



<?php $this->replace() ?>



<?php $this->section('footer') ?>
<script type="text/javascript">
    $(function(){
        var reloadPage = function() {
            $('#admin-content').load('/admin/newsletter/detail/<?= $mailing->id ?> #admin-content');
            setTimeout(reloadPage, 2000);
        };
        setTimeout(reloadPage, 2000);
    });
</script>
<?php $this->append() ?>
