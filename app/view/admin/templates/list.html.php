<?php

use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ( isset($_SESSION['user']->roles['translator']) ) ? true : false;

$filters = $vars['filters'];
?>
<div class="widget board">
    <form id="filter-form" action="/admin/templates" method="get">
        <table>
            <tr>
                <td>
                    <label for="group-filter">Filtrar agrupaci&oacute;n:</label><br />
                    <select id="group-filter" name="group">
                        <option value="">Todas las agrupaciones</option>
                    <?php foreach ($vars['groups'] as $groupId=>$groupName) : ?>
                        <option value="<?php echo $groupId; ?>"<?php if ($filters['group'] == $groupId) echo ' selected="selected"';?>><?php echo $groupName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="name-filter">Filtrar por nombre o asunto:</label><br />
                    <input type="text" id ="name-filter" name="name" value ="<?php echo $filters['name']?>" />
                </td>
                <td>
                    <label for="id-filter">Filtrar por id:</label><br />
                    <input type="text" id ="id-filter" name="id" value ="<?php echo $filters['id']?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <input type="submit" name="filter" value="Filtrar">
                </td>
            </tr>
        </table>
    </form>
</div>

<div class="widget board">
    <?php if (!empty($vars['templates'])) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Plantilla</th>
                <th>Descripción</th>
                <th><!-- traducir --></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vars['templates'] as $template) : ?>
            <tr>
                <td><a href="/admin/templates/edit/<?php echo $template->id; ?>">[Editar]</a></td>
                <td><?php echo $template->name; ?></td>
                <td><?php echo $template->purpose; ?></td>
                <?php if ($translator) : ?>
                <td><a href="/translate/template/edit/<?php echo $template->id; ?>" >[Traducir]</a></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
