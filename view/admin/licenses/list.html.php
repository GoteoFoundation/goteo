<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ACL::check('/translate') ? true : false;

$filters = $this['filters'];
?>
<!--            <a href="/add/?filter=<?php echo $filter; ?>" class="button red">Añadir licencia</a> -->

<div class="widget board">
    <form id="filter-form" action="/admin/licenses" method="get">
        <label for="group-filter">Mostrar por grupo:</label>
        <select id="group-filter" name="group" onchange="document.getElementById('filter-form').submit();">
            <option value="">Todos los grupos</option>
        <?php foreach ($this['groups'] as $groupId=>$groupName) : ?>
            <option value="<?php echo $groupId; ?>"<?php if ($filters['group'] == $groupId) echo ' selected="selected"';?>><?php echo $groupName; ?></option>
        <?php endforeach; ?>
        </select>

        <label for="icon-filter">Mostrar por tipo de retorno:</label>
        <select id="icon-filter" name="icon" onchange="document.getElementById('filter-form').submit();">
            <option value="">Todos los tipos</option>
        <?php foreach ($this['icons'] as $icon) : ?>
            <option value="<?php echo $icon->id; ?>"<?php if ($filters['icon'] == $icon->id) echo ' selected="selected"';?>><?php echo $icon->name; ?></option>
        <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="widget board">
    <?php if (!empty($this['licenses'])) : ?>
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
            <?php foreach ($this['licenses'] as $license) : ?>
            <tr>
                <td><a href="/admin/licenses/edit/<?php echo $license->id; ?>/?filter=<?php echo $filters['group']; ?>">[Editar]</a></td>
                <td><?php echo $license->name; ?></td>
                <td><img src="/view/css/license/<?php echo $license->id; ?>.png" alt="<?php echo $license->id; ?>" title="<?php echo $license->name; ?>" /></td>
                <td><?php echo $license->description; ?></td>
                <td><?php echo !empty($license->group) ? $this['groups'][$license->group] : ''; ?></td>
                <td><?php echo $license->order; ?></td>
                <td><a href="/admin/licenses/up/<?php echo $license->id; ?>/?filter=<?php echo $filters['group']; ?>">[&uarr;]</a></td>
                <td><a href="/admin/licenses/down/<?php echo $license->id; ?>/?filter=<?php echo $filters['group']; ?>">[&darr;]</a></td>
                <?php if ($translator) : ?>
                <td><a href="/translate/license/edit/<?php echo $license->id; ?>" >[Traducir]</a></td>
                <?php endif; ?>
<!--                                <td><a href="/admin/licenses/remove=<?php echo $license->id; ?>/?filter=<?php echo $filters['group']; ?>" onclick="return confirm('Seguro que deseas eliminar este registro?');">[Quitar]</a></td>  -->
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>