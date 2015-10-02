<?php

use Goteo\Library\Text;

?>
<div class="widget board">
    <?php if (!empty($vars['worthcracy'])) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- Editar--></th>
                <th>Nivel</th>
                <th>Caudal</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($vars['worthcracy'] as $worth) : ?>
            <tr>
                <td width="5%"><a href="/admin/worth/edit/<?php echo $worth->id; ?>">[Editar]</a></td>
                <td width="15%"><?php echo $worth->name; ?></td>
                <td width="15%"><?php echo $worth->amount; ?> &euro;</td>
                <td></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>IMPOSIBLE!!! No se han encontrado registros</p>
    <?php endif; ?>
</div>
