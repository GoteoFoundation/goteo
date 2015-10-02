<?php
use Goteo\Core\View,
    Goteo\Library\Text;

?>
<div class="widget">
    <h2 class="title">Edici&oacute;n de proyectos de Euskadi</h2>
    <table>
        <thead>
            <tr>
                <th>Proyecto</th> <!-- edit -->
                <th></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($vars['projects'] as $project) : ?>
            <tr>
                <td><a href="/project/<?php echo $project->id; ?>" target="_blank" title="Preview"><?php echo $project->name; ?></a></td>
                <td><a href="/project/edit/<?php echo $project->id; ?>" target="_blank">[Editar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

</div>
