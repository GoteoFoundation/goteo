<?php

$patrons = $vars['patrons'];

// TODO: esto para que es??!!
// function the_order($val, $user)
// {
//     return '<input type="text" name="order_' . $user . '"  value="' . $val . '" style="width: 20px;"/>';
// }

?>
<a href="/admin/patron/add" class="button">Nuevo apadrinamiento</a>
<a href="/admin/patron/reorder" class="button">Ordenar padrinos</a>

<div class="widget board">
    <?php if (!empty($patrons)) : ?>
        <form id="images_form" action="/admin/patron" method="post">
            <table>
                <tbody>
                <?php foreach ($patrons as $user) : ?>
                    <tr>
                        <td><?php echo $user->name; ?></td>
                        <td><a href="/admin/patron/view/<?php echo $user->id; ?>">[Apadrinamientos]</a></td>
                        <?php if($user->order) { ?>
                        <td><a href="/admin/patron/remove_home/<?php echo $user->id; ?>" style="color:red;">[Quitar de portada]</a></td>
                        <?php } else { ?>
                        <td><a href="/admin/patron/add_home/<?php echo $user->id; ?>" style="color:blue;">[Poner en portada]</a></td>
                        <?php } ?>
                    </td>
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
