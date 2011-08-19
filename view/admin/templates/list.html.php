<?php

use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ACL::check('/translate') ? true : false;
?>
<div class="widget board">
    <?php if (!empty($this['templates'])) : ?>
    <table>
        <thead>
            <tr>
                <th><!-- Editar --></th>
                <th>Plantilla</th>
                <th>Descripción</th>
                <th><!-- traducir --></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this['templates'] as $template) : ?>
            <tr>
                <td><a href="/admin/templates/edit/<?php echo $template->id; ?>">[Editar]</a></td>
                <td><?php echo $template->name; ?></td>
                <td><?php echo $template->purpose; ?></td>
                <?php if ($translator) : ?>
                <td><a href="/translate/contents/edit/template-<?php echo $template->id; ?>" target="_blank">[Traducir]</a></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>