<?php

use Goteo\Library\Text,
    Goteo\Model\User;

$filters = $vars['filters'];

?>
<a href="/admin/nodes/add" class="button">Crear nuevo nodo</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/transnodes" class="button">Asignar traductores</a>

<div class="widget board">
    <form id="filter-form" action="/admin/nodes" method="get">
        <table>
            <tr>
                <td>
                    <label for="admin-filter">Administrados por:</label><br />
                    <select id="admin-filter" name="admin" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier administrador</option>
                    <?php foreach ($vars['admins'] as $userId=>$userName) : ?>
                        <option value="<?php echo $userId; ?>"<?php if ($filters['admin'] == $userId) echo ' selected="selected"';?>><?php echo $userName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="name-filter">Nombre:</label><br />
                    <input id="name-filter" name="name" value="<?php echo $filters['name']; ?>" style="width:250px"/>
                </td>
                <td>
                    <label for="status-filter">Mostrar por estado:</label><br />
                    <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier estado</option>
                    <?php foreach ($vars['status'] as $statusId=>$statusName) : ?>
                        <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" name="filter" value="Buscar">
                </td>
            </tr>
        </table>
    </form>
</div>

<div class="widget board">
    <?php if (!empty($vars['nodes'])) : ?>
    <table>
        <thead>
            <tr>
                <th>Nodo</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th></th>
                <th></th>
                <th>Administradores</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($vars['nodes'] as $node) :
                $status = $node->active ? 'active' : 'inactive';
                if (GOTEO_ENV == 'local') {
                    $url = str_replace('http://', "http://{$node->id}.", SITE_URL);
                } else {
                    $url = $node->url;
                }
                ?>
            <tr>
                <td><a class="node-jump" href="<?php echo $url; ?>" target="_blank"><?php echo $url; ?></a></td>
                <td><?php echo $node->name; ?></td>
                <td><?php echo $vars['status'][$status]; ?></td>
                <td><a href="/admin/nodes/edit/<?php echo $node->id; ?>">[Editar]</a></td>
                <td><a href="/admin/nodes/admins/<?php echo $node->id; ?>">[Admins]</a></td>
                <td><?php echo implode(', ', $node->admins); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
