<?php

use Goteo\Library\Text,
    Goteo\Model\User\Translate;

$node = $vars['node'];
$transNode = Translate::is_legal($_SESSION['user']->id, $node, 'node') ? true : false;
$translator = ( isset($_SESSION['user']->roles['translator']) ) ? true : false;
?>
<a href="/admin/stories/add" class="button">Nueva historia exitosa</a>

<div class="widget board">
    <?php if (!empty($vars['storyed'])) : ?>
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Posición</th> <!-- order -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Editar--></th>
                <th><!-- Traducir--></th>
                <th><!-- Preview--></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($vars['storyed'] as $story) :
                $story_title = (!empty($story->name)) ? $story->name : $story->title;
                ?>
            <tr>
                <td><?php echo ($story->active) ? '<strong>'.$story_title.'</strong>' : $story_title; ?></td>
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
                <td><a href="/admin/stories/preview/<?php echo $story->id; ?>" target="_blank">[Preview]</a></td>
                <td><a href="/admin/stories/remove/<?php echo $story->id; ?>">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
