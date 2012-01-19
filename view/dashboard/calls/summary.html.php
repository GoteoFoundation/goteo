<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model\Call;

$call = $this['call'];

if (!$call instanceof  Goteo\Model\call) {
    return;
}
?>
<div class="widget">
    <a class="button red" href="/call/edit/<?php echo $call->id ?>"><?php echo Text::get('regular-edit') ?></a>
    <a class="button" href="/call/<?php echo $call->id ?>" target="_blank"><?php echo Text::get('dashboard-menu-calls-preview') ?></a>

<?php if ($call->status < 3) : ?>
    <?php if ($_SESSION['assign_mode'] === true) : ?>
        <a href="/discover/call">Seguir seleccionando proyectos</a> ó
        <a class="button" href="/dashboard/calls/projects/assign_mode/off"><?php echo Text::get('dashboard-menu-calls-assign_mode-off') ?></a>
    <?php else : ?>
        <a class="button" href="/dashboard/calls/projects/assign_mode/on"><?php echo Text::get('dashboard-menu-calls-assign_mode-on') ?></a>
    <?php endif; ?>
<?php endif; ?>

    <?php if ($call->status == 1) : ?>
    <a class="button weak" href="/call/delete/<?php echo $call->id ?>" onclick="return confirm('<?php echo Text::get('dashboard-call-delete_alert') ?>')"><?php echo Text::get('regular-delete') ?></a>
    <?php endif ?>

</div>

<div class="status">

    <div id="project-status">
        <h3><?php echo Text::get('form-call-status-title'); ?></h3>
        <ul>
            <?php foreach (call::status() as $i => $s): ?>
            <li><?php if ($i == $call->status) echo '<strong>' ?><?php echo htmlspecialchars($s) ?><?php if ($i == $call->status) echo '</strong>' ?></li>
            <?php endforeach ?>
        </ul>
    </div>

</div>

<div class="widget">
    <p>Presupuesto: <?php echo \amount_format($call->amount) ?> &euro;</p>
    <p>Queda por repartir: <?php echo \amount_format($call->rest) ?> &euro;</p>
</div>

