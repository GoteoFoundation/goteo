<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ACL::check('/translate') ? true : false;

$promos = $this['promos'];
$patrons = $this['patrons'];

function the_order($val, $user)
{
    return '<input type="text" name="order_' . $user . '"  value="' . $val . '" style="width: 20px;"/>';
}

?>
<a href="/admin/patron/add" class="button">Nuevo apadrinamiento</a>

<div class="widget board">
    <?php if (!empty($patrons) && !empty($promos)) : ?>
        <form id="images_form" action="/admin/patron" method="post">
            <table>
                <tbody>
                <?php foreach ($patrons as $user) :
                    if (empty($promos[$user->id])) continue;
                    ?>
                    <tr>
                        <td colspan="3"><h3><?php echo $user->name; ?></h3></td>
                        <td><?php echo the_order($user->order, $user->id) ?></td>
                    </tr>
                    <?php foreach ($promos[$user->id] as $promo) : ?>
                    <tr>
                        <td><a href="/project/<?php echo $promo->project; ?>" target="_blank"
                               title="Preview"><?php echo substr($promo->name, 0, 40); ?></a></td>
                        <td><?php echo $promo->status; ?></td>
                        <td><?php echo ($promo->active) ? '<strong>' . $promo->title . '</strong>' : $promo->title; ?></td>
                        <td><a href="/admin/patron/edit/<?php echo $promo->id; ?>">[Editar]</a></td>
                        <td><?php if ($promo->active) : ?>
                                <a href="/admin/patron/active/<?php echo $promo->id; ?>/off">[Ocultar]</a>
                            <?php else : ?>
                                <a href="/admin/patron/active/<?php echo $promo->id; ?>/on">[Mostrar]</a>
                            <?php endif; ?></td>
                        <?php if ($translator) : ?>
                            <td><a href="/translate/patron/edit/<?php echo $promo->id; ?>">[Traducir]</a></td>
                        <?php endif; ?>
                        <td><a href="/admin/patron/remove/<?php echo $promo->id; ?>"
                               onclick="return confirm('Aceptas eliminar este apadrinamiento?');">[Quitar]</a></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                </tbody>

            </table>
            <input type="submit" name="apply_order" value="Aplicar"/>
        </form>


        <thead>
        </thead>

        <tbody>
        <?php foreach ($this['patroned'] as $promo) : ?>
            <tr>
                <td><a href="/project/<?php echo $promo->project; ?>" target="_blank"
                       title="Preview"><?php echo substr($promo->name, 0, 40); ?></a></td>
                <td><?php echo $promo->user->name; ?></td>
                <td><?php echo ($promo->active) ? '<strong>' . $promo->title . '</strong>' : $promo->title; ?></td>
                <td><?php echo $promo->status; ?></td>
                <td><?php echo $promo->order; ?></td>
                <td><a href="/admin/patron/up/<?php echo $promo->id; ?>">[&uarr;]</a></td>
                <td><a href="/admin/patron/down/<?php echo $promo->id; ?>">[&darr;]</a></td>
                <td><a href="/admin/patron/edit/<?php echo $promo->id; ?>">[Editar]</a></td>
                <td><?php if ($promo->active) : ?>
                        <a href="/admin/patron/active/<?php echo $promo->id; ?>/off">[Ocultar]</a>
                    <?php else : ?>
                        <a href="/admin/patron/active/<?php echo $promo->id; ?>/on">[Mostrar]</a>
                    <?php endif; ?></td>
                <?php if ($translator) : ?>
                    <td><a href="/translate/patron/edit/<?php echo $promo->id; ?>">[Traducir]</a></td>
                <?php endif; ?>
                <td><a href="/admin/patron/remove/<?php echo $promo->id; ?>"
                       onclick="return confirm('Seguro que deseas eliminar este registro?');">[Quitar]</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>

        </table>
    <?php else : ?>
        <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>