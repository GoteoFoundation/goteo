<?php

use Goteo\Library\Text,
    Goteo\Model\Home;

$the_items = isset($_SESSION['admin_node']) ? Home::$node_items : Home::$items;

?>
<a href="/admin/home/add" class="button">A&ntilde;adir elemento</a>

<div class="widget board">
    <?php if (!empty($this['items'])) : ?>
    <table>
        <thead>
            <tr>
                <th>Posición</th> <!-- order -->
                <th>Elemento</th> <!-- item -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['items'] as $item) : ?>
            <tr>
                <td><?php echo $item->order; ?></td>
                <td><?php echo $the_items[$item->item]; ?></td>
                <td><a href="/admin/home/up/<?php echo $item->item; ?>">[&uarr;]</a></td>
                <td><a href="/admin/home/down/<?php echo $item->item; ?>">[&darr;]</a></td>
                <td><a href="/admin/home/remove/<?php echo $item->item; ?>">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No hay ning&uacute;n elemento en la portada, mejor a&ntilde;adir alguno</p>
    <?php endif; ?>
</div>