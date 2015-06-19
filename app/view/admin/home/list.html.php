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

$items = $vars['items'];
$new = $vars['new'];
$availables = $vars['availables'];

$side_items = $vars['side_items'];
$side_new = $vars['side_new'];
$side_availables = $vars['side_availables'];

?>
<?php /* if ($node != \GOTEO_NODE) : ?><a href="/admin/home/addside" class="button" style="margin-right: 270px;">A&ntilde;adir elemento lateral</a><?php endif; ?>
<a href="/admin/home/add" class="button">A&ntilde;adir elemento</a>
<br />
 *
 */ ?>
<?php if ($node != \GOTEO_NODE) : ?>
<div class="widget board" style="width:350px; float:left; margin-right: 5px;">
    <h4 class="title">Laterales</h4>
    <?php if (!empty($side_items)) : ?>
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
            <?php foreach ($side_items as $item) : ?>
            <tr>
                <td><?php echo $item->order; ?></td>
                <td><?php
                if (isset(Home::$admins[$item->item])) {
                    echo '<a href="'.Home::$admins[$item->item].'" style="text-decoration: underline;">'.$the_side_items[$item->item].'</a>';
                } else {
                    echo $the_side_items[$item->item]; }
                ?></td>
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

    <?php if (!empty($side_availables)) : ?>
    <form method="post" action="/admin/home" >
    <input type="hidden" name="action" value="add" />
    <input type="hidden" name="type" value="<?php echo $side_new->type ?>" />
    <input type="hidden" name="order" value="<?php echo $side_new->order ?>" />

    <p>
        <label for="home-item">Nuevo elemento:</label><br />
        <select id="home-item" name="item">
        <?php foreach ($side_availables as $item=>$name) : ?>
            <option value="<?php echo $item; ?>"><?php echo $name; ?></option>
        <?php endforeach; ?>
        </select>
        <br />
        <input type="submit" name="save" value="A&ntilde;adir" />
    </p>

    </form>
    <?php endif; ?>

</div>
<?php endif; ?>
<div class="widget board" <?php if ($node != \GOTEO_NODE) : ?>style="width:350px; float:left;"<?php endif; ?>>
    <h4 class="title">Central</h4>
    <?php if (!empty($items)) : ?>
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
            <?php foreach ($items as $item) : ?>
            <tr>
                <td><?php echo $item->order; ?></td>
                <td><?php
                if (isset(Home::$admins[$item->item])) {
                    echo '<a href="'.Home::$admins[$item->item].'" style="text-decoration: underline;">'.$the_items[$item->item].'</a>';
                } else {
                    echo $the_items[$item->item]; }
                ?></td>
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

    <?php if (!empty($availables)) : ?>
    <form method="post" action="/admin/home" >
    <input type="hidden" name="action" value="add" />
    <input type="hidden" name="type" value="<?php echo $new->type ?>" />
    <input type="hidden" name="order" value="<?php echo $new->order ?>" />

    <p>
        <label for="home-item">Nuevo elemento:</label><br />
        <select id="home-item" name="item">
        <?php foreach ($availables as $item=>$name) : ?>
            <option value="<?php echo $item; ?>"><?php echo $name; ?></option>
        <?php endforeach; ?>
        </select>
        <br />
        <input type="submit" name="save" value="A&ntilde;adir" />
    </p>

    </form>
    <?php endif; ?>

</div>

