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
                                <td>Proyecto</td>
                                <td>Creador</td>
                                <td>Estado</td>
                                <td>Progreso</td> <!-- segun estado -->
                                <td><!-- editar --></td>
                                <td><!-- Preview --></td>
                                <td><!-- Publicar --></td>
                                <td><!-- Cancelar --></td>
                                <td><!-- Rehabilitar --></td> <!-- si no edición -->
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($this['projects'] as $project) : ?>
                            <tr>
                                <td><?php echo $project->name; ?></td>
                                <td><?php echo $project->owner; ?></td>
                                <td><?php echo $this['status'][$project->status]; ?></td>
                                <td><?php echo $project->progress; ?></td>
                                <td><a href="/project/<?php echo $project->id; ?>/?edit" target="_blank">[Editar]</a></td>
                                <td><a href="/project/<?php echo $project->id; ?>" target="_blank">[Preview]</a></td>
                                <td><a href="/project/<?php echo $project->id; ?>/?publish" target="_blank">[Publicar]</a></td>
                                <td><a href="/project/<?php echo $project->id; ?>/?edit" target="_blank">[Cancelar]</a></td>
                                <td><a href="/project/<?php echo $project->id; ?>/?enable" target="_blank">[Reabrir]</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';