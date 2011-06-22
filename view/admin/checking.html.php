<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

$filters = $this['filters'];

//arrastramos los filtros
$filter = "?status={$filters['status']}&checker={$filters['checker']}";

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Revisión de proyectos</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/overview">Listado de proyectos</a></li>
                        <li><a href="/admin/managing">Revisores</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <div class="widget board">
                <form id="filter-form" action="/admin/checking" method="get">
                    <label for="status-filter">Mostrar por estado:</label>
                    <select id="status-filter" name="status" onchange="document.getElementById('filter-form').submit();">
                        <option value="">Todas</option>
                    <?php foreach ($this['status'] as $statusId=>$statusName) : ?>
                        <option value="<?php echo $statusId; ?>"<?php if ($filters['status'] == $statusId) echo ' selected="selected"';?>><?php echo $statusName; ?></option>
                    <?php endforeach; ?>
                    </select>

                    <label for="checker-filter">Asignados a:</label>
                    <select id="checker-filter" name="checker" onchange="document.getElementById('filter-form').submit();">
                        <option value="">De todos</option>
                    <?php foreach ($this['checkers'] as $checker) : ?>
                        <option value="<?php echo $checker->id; ?>"<?php if ($filters['checker'] == $checker->id) echo ' selected="selected"';?>><?php echo $checker->name; ?></option>
                    <?php endforeach; ?>
                    </select>
                </form>
            </div>

                <?php if (!empty($this['projects'])) : ?>
                    <?php foreach ($this['projects'] as $project) : ?>
                        <div class="widget board">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Proyecto</th> <!-- edit -->
                                        <th>Creador</th> <!-- mailto -->
                                        <th>%</th> <!-- segun estado -->
                                        <th>Puntos</th> <!-- segun estado -->
                                        <th>
                                            <!-- Iniciar revision si no tiene registro de revision -->
                                            <!-- Editar si tiene registro -->
                                        </th>
                                        <th><!-- Ver informe si tiene registro --></th>
                                        <th><!-- Cerar si abierta --></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td><a href="/project/<?php echo $project->project; ?>" target="_blank" title="Preview"><?php echo $project->name; ?></a></td>
                                        <td><?php echo $project->owner; ?></td>
                                        <td><?php echo $project->progress; ?></td>
                                        <td><?php echo $project->score . ' / ' . $project->max; ?></td>
                                        <?php if (!empty($project->review)) : ?>
                                        <td><a href="/admin/checking/edit/<?php echo $project->project; ?>">[Editar]</a></td>
                                        <td><a href="/admin/checking/report/<?php echo $project->review; ?>">[Ver informe]</a></td>
                                            <?php if ( $project->status > 0 ) : ?>
                                        <td><a href="/admin/checking/close/<?php echo $project->review; ?>">[Cerrar]</a></td>
                                            <?php endif; ?>
                                        <?php else : ?>
                                        <td><a href="/admin/checking/add/<?php echo $project->project; ?>">[Iniciar revision]</a></td>
                                        <td></td>
                                        <td></td>
                                        <?php endif; ?>
                                    </tr>
                                </tbody>

                            </table>

                            <?php if (!empty($project->review)) : ?>
                            <table>
                                <tr>
                                    <th>Revisor</th>
                                    <th>Listo</th>
                                    <th></th>
                                </tr>
                                <?php foreach ($project->checker as $checker) : ?>
                                <tr>
                                    <td><?php echo $checker->name; ?></td>
                                    <td><?php if ($checker->ready) echo 'Listo'; ?></td>
                                    <td><a href="/admin/checking/unassign/<?php echo $checker->user; ?>">[Desasignar]</a></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <form id="form-assign-<?php echo $project->review; ?>" action="/admin/checking/assign/<?php echo $project->review; ?>" method="post">
                                    <td colspan="2">
                                        <select name="checker">
                                            <option value="">Selecciona un nuevo revisor</option>
                                            <?php foreach ($this['checkers'] as $user) : ?>
                                            <option value="<?php echo $user->id; ?>"><?php echo $user->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><a href="#" onclick="document.getElementById('form-assign-<?php echo $project->review; ?>').submit(); return false;">[Asignar]</a></td>
                                    </form>
                                </tr>
                            </table>
                            <?php endif; ?>
                            
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                <p>No se han encontrado registros</p>
                <?php endif; ?>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';