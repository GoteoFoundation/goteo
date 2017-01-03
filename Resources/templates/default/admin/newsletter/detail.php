<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php

$mailing = $this->mailing;
$status = $mailing->getStatusObject();
$list = $this->list;
$detail = $this->detail;

$title = array(
    'receivers' => 'Destinatarios',
    'sent' => 'Enviados',
    'failed' => 'Falllidos',
    'pending' => 'Pendientes'
);
$sent = $status->percent == 100;
?>
<a href="/admin/newsletter" class="button">Volver</a>

<?php if($mailing->mail && $this->is_module_admin('Sent')): ?>
    <a href="/admin/sent/detail/<?= $mailing->mail ?>" class="button">Ver en el historial de envios</a>
<?php endif ?>

<a href="/admin/newsletter/cancel/<?= $mailing->id ?>" onclick="return confirm('El boletín se borrará, seguro que quieres continuar?')" class="button">Cancelar y eliminar este boletin</a>

<?php if ($mailing) : ?>
<div class="widget board">
        <p>
           Asunto: <strong><?= $mailing->getMail()->subject ?></strong><br />
           Creado el: <strong><?= $mailing->date ?></strong><br />
           Estado del envío automático: <span class="label label-<?= $mailing->getStatus() ?>"><?= $mailing->getStatus() ?></span>
        </p>
        <?php if(!$mailing->active && $mailing->getStatus() == 'inactive'): ?>
            <p><a style="color: white" href="/admin/newsletter/activate/<?= $mailing->id ?>" class="button">Enviar el boletin ahora</a></p>
        <?php endif ?>


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
                <td style="width:12%"><?= $this->percent_span($status->percent) ?></td>
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
        <?php foreach ($list as $recipient) : ?>
        <tr>
            <td><?= $recipient->email ?></td>
            <td><?= $recipient->name ?></td>
            <td><?= $recipient->user ?></td>
            <td><?= '<span class="label label-'. $recipient->status . '">' . $recipient->status . '</span>' . ($recipient->error ? '<br>' . $recipient->error : '') ?>
            </td>
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
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
    $(function(){
        var reloadPage = function() {
            $('#admin-content').load('/admin/newsletter/detail/<?= $mailing->id ?>?pag=<?= $this->get_query('pag') ?> #admin-content');
            setTimeout(reloadPage, 2000);
        };
        setTimeout(reloadPage, 2000);
    });
// @license-end
</script>
<?php $this->append() ?>
