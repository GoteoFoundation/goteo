<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<a href="/admin/sent" class="button">Volver al listado</a>

<a href="/mail/<?= $this->mail->getToken(false) ?>" class="button" target="_blank">Ver el mensaje</a>

<?php if($this->sender && $this->is_module_admin('Newsletter')): ?>
    <a href="/admin/newsletter/detail/<?= $this->sender ?>" class="button">Ver en el admin del boletín</a>
<?php endif ?>

<?php if($this->is_module_admin('Mailing')): ?>
    <a href="/admin/mailing/copy/<?= $this->mail->id ?>" class="button">Copiar este mensaje en comunicaciones</a>
<?php endif ?>


<div class="widget board">
<p><b>Subject:</b> <?= $this->mail->getSubject() ?></p>
<p><b>Alcance:</b> <?= number_format(sprintf('%02f', $this->readed), 2, ',', '') ?> %</p>
</div>

<?php if ($this->metric_list) : ?>
    <div class="widget board">
    <table>
        <tr>
            <th>Metric</th>
            <th>Porcentage éxito</th>
        </tr>
        <?php foreach ($this->metric_list as $collection) : ?>
        <tr>
            <td><?= $collection->metric->metric ?></td>
            <td><?= number_format(sprintf('%02f', $collection->getPercent()), 2, ',', '') ?> %</td>
        </tr>
        <?php endforeach ?>
    </table>
    </div>

<?php else : ?>
    <p>No se han encontrado registros</p>
<?php endif ?>

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
            <td><?= sprintf('%02d',round($this->stats->getEmailCollector($recipient->email)->getPercent())) ?>%</td>
            <td><?= $this->stats->getEmailOpenedLocation($recipient->email) ?></td>
            <td>
                <?php if($recipient->blacklisted) : ?>
                    <br><a href="/admin/sent/removeblacklist/<?= $this->mail->id ?>?email=<?= urlencode($recipient->email) ?>" onclick="return confirm('Se quitará el bloqueo a este email. Continuar?')">[Desbloquear]</a>
                <?php endif ?>
                <?php if($recipient->status == 'failed' || ($recipient->status == 'pending' && !$this->mail->massive)) : ?>
                    <br><a href="/admin/sent/resend/<?= $this->mail->id ?>?email=<?= urlencode($recipient->email) ?>" onclick="return confirm('Se reenviará el email. Continuar?')">[Reenviar]</a>
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
    $(function(){
        var reloadPage = function() {
            $('#admin-content').load('/admin/sent/detail/<?= $this->mail->id ?> #admin-content');
            setTimeout(reloadPage, 2000);
        };
        setTimeout(reloadPage, 2000);
    });
</script>
<?php $this->append() ?>
