<?php

use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ( isset($_SESSION['user']->roles['translator']) ) ? true : false;
?>
<a href="/admin/news/add" class="button">Nueva noticia</a>

<div class="widget board">
    <?php if (!empty($vars['news'])) : ?>
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
            <?php foreach ($vars['news'] as $new) :
                ?>
            <tr>
                <td><?php echo $new->title; ?></td>
                <td><?php echo $new->order; ?></td>
                <td><a href="/admin/news/up/<?php echo $new->id; ?>">[&uarr;]</a></td>
                <td><a href="/admin/news/down/<?php echo $new->id; ?>">[&darr;]</a></td>
                <td><a href="/admin/news/edit/<?php echo $new->id; ?>">[Editar]</a></td>
                <td><?php if (!$new->press_banner) : ?>
                <a href="/admin/news/add_press_banner/<?php echo $new->id; ?>">[Poner en Banner Prensa]</a>
                <?php else : ?>
                <a href="/admin/news/remove_press_banner/<?php echo $new->id; ?>">[Quitar de Banner Prensa]</a>
                <?php endif; ?></td>
                <td>
                <?php if ($translator) : ?>
                    <a href="/translate/news/edit/<?php echo $new->id; ?>" target="_blank">[Traducir]</a>
                <?php endif; ?>
                </td>
                <td><a href="/admin/news/remove/<?php echo $new->id; ?>">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado noticias</p>
    <?php endif; ?>
</div>
