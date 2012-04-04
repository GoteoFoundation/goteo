<?php

use Goteo\Library\Text;

$filters = $this['filters'];
?>
<a href="/admin/tasks/add" class="button red">Nueva Tarea</a>

<div class="widget board">
    <form id="filter-form" action="/admin/tasks" method="get">
        <table>
            <tr>
                <td>
                    <label for="status-filter">Mostrar por estado:</label><br />
                    <select id="status-filter" name="done" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier estado</option>
                    <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                        <option value="<?php echo $statusId; ?>"<?php if ($filters['done'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="node-filter">Del nodo:</label><br />
                    <select id="node-filter" name="node" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier nodo</option>
                    <?php foreach ($this['nodes'] as $nodeId=>$nodeName) : ?>
                        <option value="<?php echo $nodeId; ?>"<?php if ($filters['node'] == $nodeId) echo ' selected="selected"';?>><?php echo $nodeName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="user-filter">Realizadas por:</label><br />
                    <select id="user-filter" name="user" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier admin</option>
                    <?php foreach ($this['admins'] as $adminId=>$adminName) : ?>
                        <option value="<?php echo $adminId; ?>"<?php if ($filters['user'] == $adminId) echo ' selected="selected"';?>><?php echo $adminName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

    </form>
</div>

<div class="widget board">
<?php if (!empty($this['tasks'])) : ?>
    <table>
        <thead>
            <tr>
                <th></th> <!-- edit -->
                <th>Nodo</th>
                <th>Tarea</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['tasks'] as $task) : ?>
            <tr>
                <td><a href="/admin/tasks/edit/<?php echo $task->id; ?>" title="Editar">[Editar]</a></td>
                <td><strong><?php echo $this['nodes'][$task->node]; ?></strong></td>
                <td><?php echo substr($task->text, 0, 150); ?></td>
                <td><?php echo (empty($task->done)) ? 'Pendiente' : 'Realizada ('.$task->user->name.')';?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>