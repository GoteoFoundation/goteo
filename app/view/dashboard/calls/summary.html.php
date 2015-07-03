<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Call,
    Goteo\Model\Project;

$call = $vars['call'];

if (!$call instanceof  Goteo\Model\call) {
    return;
}

$callStatus = Call::status();
$status = Project::status();
?>
<div class="widget">
    <a class="button aqua" href="/call/edit/<?php echo $call->id ?>?from=dashboard"><?php echo Text::get('regular-edit') ?></a>
    <a class="button" href="/call/<?php echo $call->id ?>" target="_blank"><?php echo Text::get('dashboard-menu-calls-preview') ?></a>
    <?php if ($call->status == 1) : ?>
    <a class="button red" href="/call/delete/<?php echo $call->id ?>" onclick="return confirm('<?php echo Text::get('dashboard-call-delete_alert') ?>')"><?php echo Text::get('regular-delete') ?></a>
    <?php endif ?>

    <p>Actualmente esta convocatoria est&aacute; en estado <strong style="text-transform: uppercase;"><?php echo $callStatus[$call->status]; ?></strong></p>
</div>

<div class="widget gestrew">
    <div class="message">
        ESTO ES UNA VISUALIZACIÓN DE LOS PROYECTOS QUE RIEGAS.<br />
        LOS PROYECTOS QUE NO CONSIGAN EL MÍNIMO LIBERARÁN CAPITAL RIEGO
    </div>

    <?php if (!empty($call->projects)) : ?>
    <table>
        <tr>
            <th></th>
            <th>Impulsor</th>
            <th>Proyecto</th>
            <th>Estado</th>
            <th>Cofinanciado</th>
            <th>Riego</th>
            <th>Conseguido</th>
        </tr>
        <?php foreach ($call->projects as $proj) : ?>
        <tr>
            <td><a href="/project/<?php echo $proj->id ?>" target="_blank">[Ver]</a></td>
            <td><?php echo $proj->user->name ?></td>
            <td><a href="/project/<?php echo $proj->id ?>" target="_blank"><?php echo $proj->name ?></a></td>
            <td><?php echo $status[$proj->status] ?></td>
            <td style="text-align: right;"><?php echo $proj->amount_users ?>&euro;</td>
            <td style="text-align: right;"><?php echo $proj->amount_call ?>&euro;</td>
            <td style="text-align: right;"><?php echo $proj->amount ?>&euro;</td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else : ?>
    <p>Ning&uacute;n proyecto</p>
    <?php endif; ?>
</div>

<div class="widget">
    <p>Presupuesto: <?php echo \amount_format($call->amount) ?> &euro;</p>
    <p>Queda por repartir: <?php echo \amount_format($call->rest) ?> &euro;</p>
</div>

