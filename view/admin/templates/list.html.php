<?php

use Goteo\Library\Text;

?>
<div class="widget board">
    <table>
        <thead>
            <tr>
                <th>Plantilla</th>
                <th>Descripción</th>
                <th><!-- Editar --></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this['templates'] as $template) : ?>
            <tr>
                <td><?php echo $template->name; ?></td>
                <td><?php echo $template->purpose; ?></td>
                <td><a href="/admin/templates/edit/<?php echo $template->id; ?>">[Edit]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>