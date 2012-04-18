<?php

use Goteo\Library\Text,
    Goteo\Model\Home;

$node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;

if ($node != \GOTEO_NODE) {
    $the_items = Home::$node_items;
    $the_side_items = Home::$node_side_items;
} else {
    $the_items = Home::$items;
}

?>
<a href="/admin/home/add" class="button">A&ntilde;adir elemento</a>
<?php if ($node != \GOTEO_NODE) : ?><a href="/admin/home/addside" class="button" style="margin-left: 270px;">A&ntilde;adir elemento lateral</a><?php endif; ?>
<br />
<div class="widget board" <?php if ($node != \GOTEO_NODE) : ?>style="width:350px; float:left;"<?php endif; ?>>
    <h4 class="title">Central</h4>
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
                <td><a href="/admin/home/up/<?php echo $item->item; ?>/main">[&uarr;]</a></td>
                <td><a href="/admin/home/down/<?php echo $item->item; ?>/main">[&darr;]</a></td>
                <td><a href="/admin/home/remove/<?php echo $item->item; ?>/main">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No hay ning&uacute;n elemento en portada</p>
    <?php endif; ?>
</div>

<?php if ($node != \GOTEO_NODE) : ?>
<div class="widget board" style="width:350px; float:left; margin-left: 5px;">
    <h4 class="title">Laterales</h4>
    <?php if (!empty($this['side_items'])) : ?>
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
            <?php foreach ($this['side_items'] as $item) : ?>
            <tr>
                <td><?php echo $item->order; ?></td>
                <td><?php echo $the_side_items[$item->item]; ?></td>
                <td><a href="/admin/home/up/<?php echo $item->item; ?>/side">[&uarr;]</a></td>
                <td><a href="/admin/home/down/<?php echo $item->item; ?>/side">[&darr;]</a></td>
                <td><a href="/admin/home/remove/<?php echo $item->item; ?>/side">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No hay ning&uacute;n elemento lateral en portada</p>
    <?php endif; ?>
</div>
<?php endif; ?>