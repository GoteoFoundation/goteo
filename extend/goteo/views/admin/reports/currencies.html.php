<?php

use Goteo\Library\Text;

$data = $vars['data'];
?>
<div class="widget board">
    <p>Actuales ratios de conversión cacheados para las divisas configuradas:</p>
    <table>
        <tr>
            <th style="width: 150px;">Divisa</th>
            <th style="width: 150px;">Conversión</th>
            <th style="width: 150px;">Reversion</th>
        </tr>
        <?php foreach ($data as $ccy => $curr) :
            if ($ccy == 'EUR') continue;
            ?>
        <tr>
            <td><?php echo $curr['name']; ?></td>
            <td><?php echo '1 &euro; = '. round($curr['rate'], 5) .' ' . $curr['html']; ?> </td>
            <td><?php echo '1 '.$curr['html'].' = '. round((1 / $curr['rate']), 5) .' &euro;'; ?> </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
