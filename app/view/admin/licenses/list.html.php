<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ( isset($_SESSION['user']->roles['translator']) ) ? true : false;

$filters = $vars['filters'];
?>
<div class="widget board">
    <form id="filter-form" action="/admin/licenses" method="get">
        <label for="group-filter">Mostrar por grupo:</label>
        <select id="group-filter" name="group" onchange="document.getElementById('filter-form').submit();">
            <option value="">Todos los grupos</option>
        <?php foreach ($vars['groups'] as $groupId=>$groupName) : ?>
            <option value="<?php echo $groupId; ?>"<?php if ($filters['group'] == $groupId) echo ' selected="selected"';?>><?php echo $groupName; ?></option>
        <?php endforeach; ?>
        </select>

        <label for="icon-filter">Mostrar por tipo de retorno:</label>
        <select id="icon-filter" name="icon" onchange="document.getElementById('filter-form').submit();">
            <option value="">Todos los tipos</option>
        <?php foreach ($vars['icons'] as $icon) : ?>
            <option value="<?php echo $icon->id; ?>"<?php if ($filters['icon'] == $icon->id) echo ' selected="selected"';?>><?php echo $icon->name; ?></option>
        <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="widget board">
    <?php if (!empty($vars['licenses'])) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- Edit --></th>
                <th>Nombre</th> <!-- name -->
                <th><!-- Icon --></th>
                <th>Tooltip</th> <!-- description -->
                <th>Agrupación</th> <!-- group -->
                <th>Posición</th> <!-- order -->
                <th><!-- Move up --></th>
                <th><!-- Move down --></th>
                <th><!-- Traducir--></th>
<!--                                <td> Remove </td> -->
            </tr>
        </thead>

        <tbody>
            <?php foreach ($vars['licenses'] as $license) : ?>
            <tr>
                <td><a href="/admin/licenses/edit/<?php echo $license->id; ?>">[Editar]</a></td>
                <td><?php echo $license->name; ?></td>
                <td><img src="<?php echo SRC_URL; ?>/view/css/license/<?php echo $license->id; ?>.png" alt="<?php echo $license->id; ?>" title="<?php echo $license->name; ?>" /></td>
                <td><?php echo $license->description; ?></td>
                <td><?php echo !empty($license->group) ? $vars['groups'][$license->group] : ''; ?></td>
                <td><?php echo $license->order; ?></td>
                <td><a href="/admin/licenses/up/<?php echo $license->id; ?>">[&uarr;]</a></td>
                <td><a href="/admin/licenses/down/<?php echo $license->id; ?>">[&darr;]</a></td>
                <?php if ($translator) : ?>
                <td><a href="/translate/license/edit/<?php echo $license->id; ?>" >[Traducir]</a></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
