<?php
use Goteo\Core\View,
    Goteo\Model;

$waitfor = Model\Project::waitfor();
?>
<div class="widget projects">
    <h2 class="title">Mis Proyectos</h2>
    <?php foreach ($this['projects'] as $project) : ?>
        <div>
            <?php

            // estado en el balloon y siguiente paso en la descripcion

            // es instancia del proyecto
            // se pintan con widget horizontal 
            // muestran el estado en vez del creador // own
            // muestran el boton editar si esta en edicion // review y own
            // no muestran el boton apoyar  // review
            echo new View('view/project/widget/project.html.php', array(
                'project'   => $project,
                'balloon' => '<h4>' . htmlspecialchars($this['status'][$project->status]) . '</h4>' .
                             '<blockquote>' . $waitfor[$project->status] . '</blockquote>',
                'review' => true,
                'own'       => true
            )); ?>
        </div>
    <?php endforeach; ?>
</div>