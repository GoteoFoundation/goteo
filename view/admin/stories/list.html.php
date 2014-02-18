<?php

use Goteo\Library\Text,
    Goteo\Core\ACL;

$node = $this['node'];
$transNode = ACL::check('/translate/node/'.$node) ? true : false;
$translator = ACL::check('/translate') ? true : false;
?>
<a href="/admin/stories/add" class="button">Nueva historia exitosa</a>

<div class="widget board">
    <?php if (!empty($this['storyed'])) : ?>
    <table>
        <thead>
            <tr>
                <th><?php echo ($node == \GOTEO_NODE) ? 'Proyecto' : 'Título'; ?></th>
                <th>Estado</th> <!-- status -->
                <th>Posición</th> <!-- order -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Editar--></th>
                <th><!-- Traducir--></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['storyed'] as $story) :
                $story_title = (!empty($story->name)) ? $story->name : $story->title;
                ?>
            <tr>
                <td><?php echo ($story->active) ? '<strong>'.$story_title.'</strong>' : $story_title; ?></td>
                <td><?php echo $story->status; ?></td>
                <td><?php echo $story->order; ?></td>
                <td><a href="/admin/stories/up/<?php echo $story->id; ?>">[&uarr;]</a></td>
                <td><a href="/admin/stories/down/<?php echo $story->id; ?>">[&darr;]</a></td>
                <td><a href="/admin/stories/edit/<?php echo $story->id; ?>">[Editar]</a></td>
                <td><?php if ($story->active) : ?>
                <a href="/admin/stories/active/<?php echo $story->id; ?>/off">[Ocultar]</a>
                <?php else : ?>
                <a href="/admin/stories/active/<?php echo $story->id; ?>/on">[Mostrar]</a>
                <?php endif; ?></td>
                <td>
                <?php if ($transNode || $translator) : ?>
                <a href="/translate/stories/edit/<?php echo $story->id; ?>" target="_blank">[Traducir]</a>
                <?php endif; ?>
                </td>
                <td><a href="/admin/stories/remove/<?php echo $story->id; ?>">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
