<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

$filters = $this['filters'];

//arrastramos los filtros
$filter = "?status={$filters['status']}&category={$filters['category']}";

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Listado de proyectos</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['errors']) || !empty($this['success'])) : ?>
                <div class="widget">
                    <p>
                        <?php echo implode(',', $this['errors']); ?>
                        <?php echo implode(',', $this['success']); ?>
                    </p>
                </div>
            <?php endif; ?>

            <div class="widget board">
                <form id="filter-form" action="/admin/overview" method="get">
                    <label for="status-filter">Mostrar por estado:</label>
                    <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Todos los estados</option>
                    <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                        <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
                    <?php endforeach; ?>
                    </select>

                    <label for="category-filter">De la categoría:</label>
                    <select id="category-filter" name="category" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Cualquier categoría</option>
                    <?php foreach ($this['categories'] as $categoryId=>$categoryName) : ?>
                        <option value="<?php echo $categoryId; ?>"<?php if ($filters['category'] == $categoryId) echo ' selected="selected"';?>><?php echo $categoryName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="widget board">
                <?php if (!empty($this['projects'])) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>Proyecto</th> <!-- edit -->
                            <th>Creador</th> <!-- mailto -->
                            <th>Estado</th>
                            <th>%</th> <!-- segun estado -->
                            <th>Días</th> <!-- segun estado -->
                            <th>Conseguido</th> <!-- segun estado -->
                            <th>Mínimo</th> <!-- segun estado -->
                            <th><!-- Editar --></th>
                            <th><!-- Publicar --></th> <!-- si revisado -->
                            <th><!-- Cancelar --></th> <!-- si no cancelado -->
                            <th><!-- Rehabilitar --></th> <!-- si no edición -->
<!--                                <th>Financiado</th> si está en campaña -->
<!--                                <th>Cumplido</th> si está financiado -->
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($this['projects'] as $project) : ?>
                        <tr>
                            <td><a href="/project/<?php echo $project->id; ?>" target="_blank" title="Preview"><?php echo $project->name; ?></a></td>
                            <td><?php echo $project->user->name; ?></td>
                            <td><?php echo $this['status'][$project->status]; ?></td>
                            <td><?php if ($project->status < 3)  echo $project->progress; ?></td>
                            <td><?php if ($project->status == 3) echo $project->days; ?></td>
                            <td><?php if ($project->status > 2) echo $project->invested; ?></td>
                            <td><?php if ($project->status > 2) echo $project->mincost; ?></td>
                            <td><a href="/project/edit/<?php echo $project->id; ?>" target="_blank">[Editar]</a></td>
                            <td><?php if ($project->status < 3) : ?><a href="<?php echo "/admin/overview/publish/{$project->id}{$filter}"; ?>">[Publicar]</a><?php endif; ?></td>
                            <td><?php if ($project->status != 5) : ?><a href="<?php echo "/admin/overview/cancel/{$project->id}{$filter}"; ?>">[Cancelar]</a><?php endif; ?></td>
                            <td><?php if ($project->status > 1) : ?><a href="<?php echo "/admin/overview/enable/{$project->id}{$filter}"; ?>">[Reabrir]</a><?php endif; ?></td>
<!--                                <td><?php if ($project->status == 3) : ?><a href="<?php echo "/admin/overview/complete/{$project->id}{$filter}"; ?>">[Financiado]</a><?php endif; ?></td> -->
<!--                                <td><?php if ($project->status == 4) : ?><a href="<?php echo "/admin/overview/fulfill/{$project->id}{$filter}"; ?>">[Cumplido]</a><?php endif; ?></td> -->
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
                <?php else : ?>
                <p>No se han encontrado registros</p>
                <?php endif; ?>
            </div>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';