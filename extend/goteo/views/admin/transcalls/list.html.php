<?php

use Goteo\Library\Text;

$filters = $vars['filters'];
?>
<a href="/admin/transcalls/add" class="button">Nueva Convocatoria para traducir</a>

<div class="widget board">
<form id="filter-form" action="/admin/transcalls" method="get">
    <label for="owner-filter">Convocatorias del usuario:</label>
    <select id="owner-filter" name="owner" onchange="document.getElementById('filter-form').submit();">
        <option value="">Todos los convocadores</option>
    <?php foreach ($vars['owners'] as $ownerId=>$ownerName) : ?>
        <option value="<?php echo $ownerId; ?>"<?php if ($filters['owner'] == $ownerId) echo ' selected="selected"';?>><?php echo $ownerName; ?></option>
    <?php endforeach; ?>
    </select>

    <label for="translator-filter">Asignados a traductor:</label>
    <select id="translator-filter" name="translator" onchange="document.getElementById('filter-form').submit();">
        <option value="">Todos los traductores</option>
    <?php foreach ($vars['translators'] as $translator) : ?>
        <option value="<?php echo $translator->id; ?>"<?php if ($filters['translator'] == $translator->id) echo ' selected="selected"';?>><?php echo $translator->name; ?></option>
    <?php endforeach; ?>
    </select>
</form>
</div>

<!-- Convocatorias con la traducción activa -->
<?php if (!empty($vars['calls'])) : ?>
        <div class="widget board">
            <table>
                <thead>
                    <tr>
                        <th width="5%"><!-- Editar y asignar --></th>
                        <th width="55%">Convocatoria</th> <!-- edit -->
                        <th width="30%">Creador</th>
<!--                        <th width="10%">Idioma</th> -->
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($vars['calls'] as $call) : ?>
                    <tr>
                        <td><a href="/admin/transcalls/edit/<?php echo $call->id; ?>">[Editar]</a></td>
                        <td><a href="/call/<?php echo $call->id; ?>" target="_blank" title="Preview"><?php echo $call->name; ?></a></td>
                        <td><?php echo $call->user->name; ?></td>
<!--                        <td><?php echo $call->lang; ?></td> -->
                    </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>

        </div>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>
