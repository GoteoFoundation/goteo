<?php

use Goteo\Library\Text;

$filters = $this['filters'];

//arrastramos los filtros
$filter = "?status={$filters['status']}&category={$filters['category']}&owner={$filters['owner']}&name={$filters['name']}&order={$filters['order']}";

?>
<div class="widget board">
    <form id="filter-form" action="/admin/projects" method="get">
        <table>
            <tr>
                <td>
                    <label for="owner-filter">Del autor:</label><br />
                    <select id="owner-filter" name="owner" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier autor</option>
                    <?php foreach ($this['owners'] as $ownerId=>$ownerName) : ?>
                        <option value="<?php echo $ownerId; ?>"<?php if ($filters['owner'] == $ownerId) echo ' selected="selected"';?>><?php echo $ownerName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="category-filter">De la categoría:</label><br />
                    <select id="category-filter" name="category" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier categoría</option>
                    <?php foreach ($this['categories'] as $categoryId=>$categoryName) : ?>
                        <option value="<?php echo $categoryId; ?>"<?php if ($filters['category'] == $categoryId) echo ' selected="selected"';?>><?php echo $categoryName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="name-filter">Nombre:</label><br />
                    <input id="name-filter" name="name" value="<?php echo $filters['name']; ?>" style="width:250px"/>
                </td>
                <td>
                    <label for="status-filter">Mostrar por estado:</label><br />
                    <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Todos los estados</option>
                    <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                        <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <label for="order-filter">Ver por:</label><br />
                    <select id="order-filter" name="order" onchange="document.getElementById('filter-form').submit();">
                    <?php foreach ($this['orders'] as $orderId=>$orderName) : ?>
                        <option value="<?php echo $orderId; ?>"<?php if ($filters['order'] == $orderId) echo ' selected="selected"';?>><?php echo $orderName; ?></option>
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
    <?php if (!empty($this['projects'])) : ?>
    <table>
        <thead>
            <tr>
                <th>Proyecto</th> <!-- edit -->
                <th>Creador</th> <!-- mailto -->
                <th>Recibido</th> <!-- enviado a revision -->
                <th>Estado</th>
                <th>%</th> <!-- segun estado -->
                <th>Días</th> <!-- segun estado -->
                <th>Conseguido</th> <!-- segun estado -->
                <th>Mínimo</th> <!-- segun estado -->
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['projects'] as $project) : ?>
            <tr>
                <td><a href="/project/<?php echo $project->id; ?>" target="_blank" title="Preview"><?php echo $project->name; ?></a></td>
                <td><?php echo $project->user->name; ?></td>
                <td><?php echo date('d-m-Y', strtotime($project->updated)); ?></td>
                <td><?php echo $this['status'][$project->status]; ?></td>
                <td><?php if ($project->status < 3)  echo $project->progress; ?></td>
                <td><?php if ($project->status == 3) echo "$project->days (round {$project->round})"; ?></td>
                <td><?php echo $project->invested; ?></td>
                <td><?php echo $project->mincost; ?></td>
            </tr>
            <tr>
                <td colspan="7"> >>> Acciones:
                    <a href="/project/edit/<?php echo $project->id; ?>" target="_blank">[Editar]</a>
                    <?php if ($project->status == 1) : ?><a href="<?php echo "/admin/projects/review/{$project->id}{$filter}"; ?>">[A revisión]</a><?php endif; ?>
                    <?php if ($project->status < 3) : ?><a href="<?php echo "/admin/projects/publish/{$project->id}{$filter}"; ?>">[Publicar]</a><?php endif; ?>
                    <?php if ($project->status > 1) : ?><a href="<?php echo "/admin/projects/enable/{$project->id}{$filter}"; ?>">[Reabrir]</a><?php endif; ?>
                    <?php if ($project->status == 4) : ?><a href="<?php echo "/admin/projects/fulfill/{$project->id}{$filter}"; ?>">[Retorno Cumplido]</a><?php endif; ?>
                    <a href="<?php echo "/admin/projects/cancel/{$project->id}{$filter}"; ?>" onclick="return confirm('El proyecto va a quedar DESCARTADO permanentemente, ¿seguro que hacemos eso?');">[Descartar]</a>
                    <a href="<?php echo "/admin/projects/dates/{$project->id}{$filter}"; ?>">[Cambiar fechas]</a>
                </td>
            </tr>
            <tr>
                <td colspan="7"><hr /></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>