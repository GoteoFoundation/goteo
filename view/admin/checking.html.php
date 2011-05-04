<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Revisión de proyectos</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>


                    <table>
                        <thead>
                            <tr>
                                <td>Proyecto</td> <!-- edit -->
                                <td>Creador</td> <!-- mailto -->
                                <td>Estado</td>
                                <td>%</td> <!-- segun estado -->
                                <td>Días</td> <!-- segun estado -->
                                <td>Conseguido</td> <!-- segun estado -->
                                <td>Mínimo</td> <!-- segun estado -->
                                <td><!-- Preview --></td>
                                <td><!-- Publicar --></td> <!-- si revisado -->
                                <td><!-- Cancelar --></td> <!-- si no cancelado -->
                                <td><!-- Rehabilitar --></td> <!-- si no edición -->
                                <td><!-- Financiado --></td> <!-- si está en campaña -->
                                <td><!-- Cumplido --></td> <!-- si está financiado -->
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($this['projects'] as $project) : ?>
                            <tr>
                                <td><a href="/project/<?php echo $project->id; ?>/?edit" target="_blank"><?php echo $project->name; ?></a></td>
                                <td><a href="mailto:<?php echo $project->user->email; ?>"><?php echo $project->user->name; ?></a></td>
                                <td><?php echo $this['status'][$project->status]; ?></td>
                                <td><?php if ($project->status < 3)  echo $project->progress; ?></td>
                                <td><?php if ($project->status == 3) echo $project->days; ?></td>
                                <td><?php if ($project->status > 2) echo $project->invested; ?></td>
                                <td><?php if ($project->status > 2) echo $project->mincost; ?></td>
                                <td><a href="/project/<?php echo $project->id; ?>" target="_blank">[Preview]</a></td>
                                <td><?php if ($project->status < 3) : ?><a href="?publish=<?php echo $project->id; ?>">[Publicar]</a><?php endif; ?></td>
                                <td><?php if ($project->status != 5) : ?><a href="?cancel=<?php echo $project->id; ?>">[Cancelar]</a><?php endif; ?></td>
                                <td><?php if ($project->status == 5) : ?><a href="?enable=<?php echo $project->id; ?>">[Reabrir]</a><?php endif; ?></td>
                                <td><?php if ($project->status == 3) : ?><a href="?complete=<?php echo $project->id; ?>">[Financiado]</a><?php endif; ?></td>
                                <td><?php if ($project->status == 4) : ?><a href="?fulfill=<?php echo $project->id; ?>">[Cumplido]</a><?php endif; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';