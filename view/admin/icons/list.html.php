<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ACL::check('/translate') ? true : false;
$filters = $this['filters'];
?>
<div class="widget board">
    <form id="groupfilter-form" action="/admin/icons" method="get">
        <label for="group-filter">Mostrar los tipos para:</label>
        <select id="group-filter" name="group" onchange="document.getElementById('groupfilter-form').submit();">
            <option value="">Todo</option>
        <?php foreach ($this['groups'] as $groupId=>$groupName) : ?>
            <option value="<?php echo $groupId; ?>"<?php if ($filters['group'] == $groupId) echo ' selected="selected"';?>><?php echo $groupName; ?></option>
        <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="widget board">
    <?php if (!empty($this['icons'])) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Nombre</th> <!-- name -->
                <th>Tooltip</th> <!-- descripcion -->
                <th>Agrupación</th> <!-- group -->
                <th><!-- Traducir--></th>
<!--                        <th> Remove </th>  -->
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['icons'] as $icon) : ?>
            <tr>
                <td><a href="/admin/icons/edit/<?php echo $icon->id; ?>">[Editar]</a></td>
                <td><?php echo $icon->name; ?></td>
                <td><?php echo $icon->description; ?></td>
                <td><?php echo !empty($icon->group) ? $this['groups'][$icon->group] : 'Ambas'; ?></td>
                <?php if ($translator) : ?>
                <td><a href="/translate/icon/edit/<?php echo $icon->id; ?>" >[Traducir]</a></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>