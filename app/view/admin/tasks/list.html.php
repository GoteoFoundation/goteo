<?php

use Goteo\Library\Text;

$filters = $vars['filters'];
?>
<a href="/admin/tasks/add" class="button">Nueva Tarea</a>

<div class="widget board">
    <form id="filter-form" action="/admin/tasks" method="get">
        <table>
            <tr>
                <td>
                    <label for="status-filter">Mostrar por estado:</label><br />
                    <select id="status-filter" name="done" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier estado</option>
                    <?php foreach ($vars['status'] as $statusId=>$statusName) : ?>
                        <option value="<?php echo $statusId; ?>"<?php if ($filters['done'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <?php if (!isset($_SESSION['admin_node']) && $_SESSION['admin_node'] != \GOTEO_NODE) : ?>
                <td>
                    <label for="node-filter">Del nodo:</label><br />
                    <select id="node-filter" name="node" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier nodo</option>
                    <?php foreach ($vars['nodes'] as $nodeId=>$nodeName) : ?>
                        <option value="<?php echo $nodeId; ?>"<?php if ($filters['node'] == $nodeId) echo ' selected="selected"';?>><?php echo $nodeName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <?php endif; ?>
                <td>
                    <label for="user-filter">Realizadas por:</label><br />
                    <select id="user-filter" name="user" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier admin</option>
                    <?php foreach ($vars['admins'] as $adminId=>$adminName) : ?>
                        <option value="<?php echo $adminId; ?>"<?php if ($filters['user'] == $adminId) echo ' selected="selected"';?>><?php echo $adminName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

    </form>
</div>

<div class="widget board">
<?php if (!empty($vars['tasks'])) : ?>
    <table>
        <thead>
            <tr>
                <th></th> <!-- edit -->
                <th>Nodo</th>
                <th>Tarea</th>
                <th>Estado</th>
                <th></th> <!-- remove -->
            </tr>
        </thead>

        <tbody>
            <?php foreach ($vars['tasks'] as $task) : ?>
            <tr>
                <td><a href="/admin/tasks/edit/<?php echo $task->id; ?>" title="Editar">[Editar]</a></td>
                <td><strong><?php echo $vars['nodes'][$task->node]; ?></strong></td>
                <td><?php echo substr($task->text, 0, 150); ?></td>
                <td><?php echo (empty($task->done)) ? 'Pendiente' : 'Realizada ('.$task->user->name.')';?></td>
                <td><a href="/admin/tasks/remove/<?php echo $task->id; ?>" title="Eliminar" onclick="return confirm('La tarea se eliminará irreversiblemente, ok?')">[Eliminar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
