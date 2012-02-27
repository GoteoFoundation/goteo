<?php

use Goteo\Library\Text,
    Goteo\Model\User;

$filters = $this['filters'];

//arrastramos los filtros
$filter = "?status={$filters['status']}&admin={$filters['admin']}&name={$filters['name']}";

$admins = User::getAdmins();

?>
<a href="/admin/nodes/add" class="button red">Crear nuevo nodo</a>

<div class="widget board">
    <form id="filter-form" action="/admin/nodes" method="get">
        <table>
            <tr>
                <td>
                    <label for="admin-filter">Administrados por:</label><br />
                    <select id="admin-filter" name="admin" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier administrador</option>
                    <?php foreach ($this['admins'] as $userId=>$userName) : ?>
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
                    <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
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
    <?php if (!empty($this['nodes'])) : ?>
    <table>
        <thead>
            <tr>
                <th>Nodo</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Administrador</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['nodes'] as $node) :
                $status = $node->active ? 'active' : 'inactive';
                $url = str_replace('http://', "http://{$node->id}.", SITE_URL);
                ?>
            <tr>
                <td><a class="node-jump" href="<?php echo $url; ?>" target="_blank"><?php echo $url; ?></a></td>
                <td><?php echo $node->name; ?></td>
                <td><?php echo $this['status'][$status]; ?></td>
                <td><?php echo $admins[$node->admin]; ?></td>
                <td><a href="/admin/nodes/edit/<?php echo $node->id; ?>">[Editar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>