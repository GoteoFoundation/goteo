<?php

use Goteo\Library\Text;

$call = $this['call'];
$conf = $this['conf'];

$values = array(
    'normal' => Text::get('call-conf-normal'),
    'unlimited' => Text::get('call-conf-unlimited'),
    'minimum' => Text::get('call-conf-minimum'),
    'none' => Text::get('call-conf-none')
);

$buzz = array(
    'buzz_first' => Text::get('call-conf-buzz_first'),
    'buzz_own' => Text::get('call-conf-buzz_own'),
    'buzz_mention' => Text::get('call-conf-buzz_mention')
);

$cbzz =array();
$cbzz[] = ($conf->buzz_own) ? $buzz['buzz_own'] : 'No muestra '.$buzz['buzz_own'];
$cbzz[] = ($conf->buzz_mention) ? $buzz['buzz_mention'] : 'No muestra '.$buzz['buzz_mention'];
if ($conf->buzz_first) $cbzz[] = $buzz['buzz_first'];


?>
<div class="widget">
    <h2>Riego máximo que puede conseguir cada proyecto</h2>
    <table>
        <tr>
            <th>En primera ronda:</th>
            <td style="width: 500px;"><?php echo $values[$conf->limit1]; ?></td>
        </tr>
        <tr>
            <th>En segunda ronda:</th>
            <td style="width: 500px;"><?php echo $values[$conf->limit2]; ?></td>
        </tr>
    </table>
</div>

<?php if (!empty($cbzz)) : ?>
<div class="widget">
    <table>
        <tr>
            <th>En carrusel de tweets aparecen:</th>
            <td><?php echo implode(', ', $cbzz); ?></td>
        </tr>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($conf->applied)) : ?>
<div class="widget">
    <table>
        <tr>
            <th><?php echo Text::get('call-conf-applied'); ?></th>
            <td><?php echo $conf->applied; ?></td>
        </tr>
    </table>
</div>
<?php endif; ?>