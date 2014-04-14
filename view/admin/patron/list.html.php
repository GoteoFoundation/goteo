<?php

$patrons = $this['patrons'];

function the_order($val, $user)
{
    return '<input type="text" name="order_' . $user . '"  value="' . $val . '" style="width: 20px;"/>';
}

?>
<a href="/admin/patron/add" class="button">Nuevo apadrinamiento</a>

<div class="widget board">
    <?php if (!empty($patrons)) : ?>
        <form id="images_form" action="/admin/patron" method="post">
            <table>
                <tbody>
                <?php foreach ($patrons as $user) : ?>
                    <tr>
                        <td><?php echo the_order($user->order, $user->id) ?></td>
                        <td><?php echo $user->name; ?></td>
                        <td><a href="/admin/patron/view/<?php echo $user->id; ?>">[Apadrinamientos]</a></td>
                        <td><?php if (isset($this['homes'][$post->id])) {
                        echo '<a href="/admin/blog/remove_home/'.$post->id.'" style="color:red;">[Quitar de portada]</a>';
                    } elseif ($post->publish) {
                        echo '<a href="/admin/blog/add_home/'.$post->id.'" style="color:blue;">[Poner en portada]</a>';
                    } ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

            </table>
            <input type="submit" name="apply_order" value="Aplicar"/>
        </form>
    <?php else : ?>
        <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>