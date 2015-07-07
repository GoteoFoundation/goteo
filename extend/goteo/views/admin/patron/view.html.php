<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ( isset($_SESSION['user']->roles['translator']) ) ? true : false;

$promos = $vars['promos'];
$patron = $vars['patron'];

?>
<a href="/admin/patron" class="button">Volver a la lista</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/patron/add?user=<?php echo $patron->id; ?>" class="button">Nuevo apadrinamiento</a>

<div class="widget board">
    <h3>Proyectos apadrinados por <?php echo $patron->name; ?></h3>
    <?php if (!empty($promos)) : ?>
        <table>
            <tbody>
            <?php foreach ($promos as $promo) : ?>
                <tr>
                    <td><a href="/project/<?php echo $promo->project; ?>" target="_blank"
                           title="Preview"><?php echo substr($promo->name, 0, 40); ?></a></td>
                    <td><?php echo $promo->status; ?></td>
                    <td><?php echo ($promo->active) ? '<strong>' . $promo->title . '</strong>' : $promo->title; ?></td>
                    <td><a href="/admin/patron/edit/<?php echo $promo->id; ?>">[Editar]</a></td>
                    <td><?php if ($promo->active) : ?>
                            <a href="/admin/patron/active/<?php echo $promo->id; ?>/off?user=<?php echo $patron->id; ?>">[Ocultar]</a>
                        <?php else : ?>
                            <a href="/admin/patron/active/<?php echo $promo->id; ?>/on?user=<?php echo $patron->id; ?>">[Mostrar]</a>
                        <?php endif; ?></td>
                    <?php if ($translator) : ?>
                        <td><a href="/translate/patron/edit/<?php echo $promo->id; ?>">[Traducir]</a></td>
                    <?php endif; ?>
                    <td><a href="/admin/patron/remove/<?php echo $promo->id; ?>?user=<?php echo $patron->id; ?>"
                           onclick="return confirm('Aceptas eliminar este apadrinamiento?');">[Quitar]</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>
    <?php else : ?>
        <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
