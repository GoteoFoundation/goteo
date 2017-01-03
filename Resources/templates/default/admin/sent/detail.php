<?php

$this->layout('admin/layout');

$mail = $this->mail;
$success = 100;
if($sender = $mail->getSender()) {
    $success = floor($sender->getStatusObject()->percent_success);
}

 ?>

<?php $this->section('admin-content') ?>

<a href="/admin/sent" class="button">Volver al listado</a>

<a href="/mail/<?= $mail->getToken(false) ?>" class="button" target="_blank">Ver el mensaje</a>

<?php if($this->sender && $this->is_module_admin('Newsletter')): ?>
    <a href="/admin/newsletter/detail/<?= $this->sender ?>" class="button">Ver en el admin del boletín</a>
<?php endif ?>

<?php if($this->is_module_admin('Mailing')): ?>
    <a href="/admin/mailing/copy/<?= $mail->id ?>" class="button">Copiar este mensaje en comunicaciones</a>
<?php endif ?>


<div id="detail-top">

<div class="widget board">
<p><b>Subject:</b> <?= $mail->getSubject() ?></p>
<?php if($mail->template): ?>
<p><b>Template:</b> <td><?= $this->templates[$mail->template] ?></td></p>
<?php endif ?>
<p><b>Date:</b> <?= $mail->date ?></p>
<p><b>Status:</b> <?= '<span class="label label-'. $mail->getStatus() . '">' . $mail->getStatus() . '</span>' ?>
                  <?= $this->percent_span($success) ?></td>
<p><b>Alcance:</b> <?= $this->percent_span($this->readed, 2) ?> <span class="label"><?= $this->readed_hits ?> hits</span></p>
</div>

<?php if ($this->metric_list) : ?>
    <div class="widget board">
    <table>
        <tr>
            <th>Metric</th>
            <th>Porcentaje éxito</th>
        </tr>
        <?php foreach ($this->metric_list as $collection) : ?>
        <tr>
            <td><?= $collection->metric->metric ?></td>
            <td><?= $this->percent_span($collection->getPercent()) ?> <span class="label"><?= $collection->non_zero ?> hits</span></td>
        </tr>
        <?php endforeach ?>
    </table>
    </div>

<?php else : ?>
    <p>No hay métricas</p>
<?php endif ?>
</div>

<h3>Listado completo de los receptores</h3>
<?php if ($this->user_list) : ?>
    <div class="widget board">
    <p>Total de receptores: <?= $this->total ?></p>
    <table>
        <tr>
            <th>Email</th>
            <th>Nombre</th>
            <th>ID Usuario</th>
            <th>Estado</th>
            <th>Leido</th>
            <th>% links</th>
            <th>Location</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($this->user_list as $recipient) :
            $opened = $this->stats->getEmailOpenedCounter($recipient->email);
        ?>
        <tr>
            <td><?= $recipient->email ?></td>
            <td><?= $recipient->name ?></td>
            <td><?= $recipient->user ?></td>
            <td><?= '<span class="label label-'. $recipient->status . '">' . $recipient->status . '</span>' . ($recipient->error ? '<br>' . $recipient->error : '') ?>
            </td>
            <td><?= '<span class="label'. ($opened ? ' label-success' : '') . '">' . $opened .'</span>' ?></td>
            <td><?= $this->percent_span($this->stats->getEmailCollector($recipient->email)->getPercent()) ?></td>
            <td><?= $this->stats->getEmailOpenedLocation($recipient->email) ?></td>
            <td>
                <?php if($recipient->blacklisted) : ?>
                    <br><a href="/admin/sent/removeblacklist/<?= $mail->id ?>?email=<?= urlencode($recipient->email) ?>" onclick="return confirm('Se quitará el bloqueo a este email. Continuar?')">[Desbloquear]</a>
                <?php endif ?>
                <?php if($recipient->status == 'failed' || ($recipient->status == 'pending' && !$mail->massive)) : ?>
                    <br><a href="/admin/sent/resend/<?= $mail->id ?>?email=<?= urlencode($recipient->email) ?>" onclick="return confirm('Se reenviará el email. Continuar?')">[Reenviar]</a>
                <?php endif ?>
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
            $('#detail-top').load('/admin/sent/detail/<?= $mail->id ?> #detail-top');
            setTimeout(reloadPage, 2000);
        };
        setTimeout(reloadPage, 2000);
    });
// @license-end
</script>
<?php $this->append() ?>
