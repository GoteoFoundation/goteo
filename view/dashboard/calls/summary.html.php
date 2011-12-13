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
    <p><strong><?php echo $call->name ?></strong></p>
    <a class="button red" href="/call/edit/<?php echo $call->id ?>"><?php echo Text::get('regular-edit') ?></a>
    <a class="button" href="/call/<?php echo $call->id ?>" target="_blank"><?php echo Text::get('dashboard-menu-calls-preview') ?></a>
    <?php if ($call->status == 1) : ?>
    <a class="button weak" href="/call/delete/<?php echo $call->id ?>" onclick="return confirm('<?php echo Text::get('dashboard-call-delete_alert') ?>')"><?php echo Text::get('regular-delete') ?></a>
    <?php endif ?>
</div>

<div class="status">

    <div id="call-status">
        <h3><?php echo Text::get('form-call-status-title'); ?></h3>
        <ul>
            <?php foreach (call::status() as $i => $s): ?>
            <li><?php if ($i == $call->status) echo '<strong>' ?><?php echo htmlspecialchars($s) ?><?php if ($i == $call->status) echo '</strong>' ?></li>
            <?php endforeach ?>
        </ul>
    </div>

</div>

<div class="widget">
    <p>Presupuesto: tanto</p>
    <p>Queda por repartir: tanto</p>
</div>

