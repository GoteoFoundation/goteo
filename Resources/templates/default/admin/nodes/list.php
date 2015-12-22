<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>
<?php

$filters = $this->filters;

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
                    <?php foreach ($this->admins as $userId => $userName) : ?>
                        <option value="<?= $userId ?>"<?php if ($filters['admin'] == $userId) echo ' selected="selected"';?>><?= $userName ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
                <td>
                    <label for="name-filter">Nombre:</label><br />
                    <input id="name-filter" name="name" value="<?= $filters['name'] ?>" style="width:250px"/>
                </td>
                <td>
                    <label for="status-filter">Mostrar por estado:</label><br />
                    <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier estado</option>
                    <?php foreach ($this->status as $statusId => $statusName) : ?>
                        <option value="<?= $statusId ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?= $statusName ?></option>
                    <?php endforeach ?>
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
    <br clear="both" />
    <a href="/admin/nodes?reset=filters">[<?= $this->text('admin-remove-filters') ?>]</a>
</div>

<div class="widget board">
    <?php if ($this->nodes) : ?>
    <table>
        <thead>
            <tr>
                <th>Nodo</th>
                <th>Nombre</th>
                <th>URL</th>
                <th>Estado</th>
                <th></th>
                <th></th>
                <th>Administradores</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this->nodes as $node) :
                $status = $node->active ? 'active' : 'inactive';
                $url = $node->getUrl();
                ?>
            <tr>
                <td><a class="node-jump" href="<?= $url ?>" target="_blank"><?= $node->id ?></a></td>
                <td><?= $node->name ?></td>
                <td><?= $url ?></td>
                <td><?= $this->status[$status] ?></td>
                <td><a href="/admin/nodes/edit/<?= $node->id ?>">[Editar]</a></td>
                <td><a href="/admin/nodes/admins/<?= $node->id ?>">[Admins]</a></td>
                <td><?= implode(', ', $node->admins) ?></td>
            </tr>
            <?php endforeach ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif ?>
</div>

<?php $this->replace() ?>
