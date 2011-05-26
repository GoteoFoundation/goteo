<?php

use Goteo\Library\Text;

$bodyClass = 'project-show';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Revisión de proyectos</h2>
            </div>

            <div class="sub-menu">
                <div class="project-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

                <div class="widget">
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
                                <th><!-- Financiado --></th> <!-- si está en campaña -->
                                <th><!-- Cumplido --></th> <!-- si está financiado -->
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($this['projects'] as $project) : ?>
                            <tr>
                                <td><a href="/project/<?php echo $project->id; ?>" target="_blank" title="Preview"><?php echo $project->name; ?></a></td>
                                <td><a href="mailto:<?php echo $project->user->email; ?>" title="Email"><?php echo $project->user->name; ?></a></td>
                                <td><?php echo $this['status'][$project->status]; ?></td>
                                <td><?php if ($project->status < 3)  echo $project->progress; ?></td>
                                <td><?php if ($project->status == 3) echo $project->days; ?></td>
                                <td><?php if ($project->status > 2) echo $project->invested; ?></td>
                                <td><?php if ($project->status > 2) echo $project->mincost; ?></td>
                                <td><a href="/project/edit/<?php echo $project->id; ?>" target="_blank">[Editar]</a></td>
                                <td><?php if ($project->status < 3) : ?><a href="/admin/checking/publish/<?php echo $project->id; ?>">[Publicar]</a><?php endif; ?></td>
                                <td><?php if ($project->status != 5) : ?><a href="/admin/checking/cancel/<?php echo $project->id; ?>">[Cancelar]</a><?php endif; ?></td>
                                <td><?php if ($project->status == 5 || $project->status == 2) : ?><a href="/admin/checking/enable/<?php echo $project->id; ?>">[Reabrir]</a><?php endif; ?></td>
                                <td><?php if ($project->status == 3) : ?><a href="/admin/checking/complete/<?php echo $project->id; ?>">[Financiado]</a><?php endif; ?></td>
                                <td><?php if ($project->status == 4) : ?><a href="/admin/checking/fulfill/<?php echo $project->id; ?>">[Cumplido]</a><?php endif; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';