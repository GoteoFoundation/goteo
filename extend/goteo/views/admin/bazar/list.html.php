<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ( isset($_SESSION['user']->roles['translator']) ) ? true : false;
?>
<a href="/admin/bazar/add" class="button">Nuevo Elemento</a>&nbsp;&nbsp;&nbsp;<a href="/bazaar" class="button" target="_blank">Ver catalogo</a>

<div class="widget board">
    <?php if (!empty($vars['items'])) : ?>
    <table>
        <thead>
            <tr>
                <th></th> <!-- preview -->
                <th>Titulo</th>
                <th></th> <!--image -->
                <th>Importe</th>
                <th>Estado</th>
                <th>Posición</th> <!-- order -->
                <th><!-- Subir --></th>
                <th><!-- Bajar --></th>
                <th><!-- Editar--></th>
                <th><!-- On/Off --></th>
                <th><!-- Traducir--></th>
                <th><!-- Quitar--></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($vars['items'] as $promo) : ?>
            <tr>
                <td><a href="/bazaar/<?php echo $promo->id; ?>" target="_blank" title="Preview">[Ver]</a></td>
                <td><?php echo ($promo->active) ? '<strong>'.$promo->title.'</strong>' : $promo->title; ?></td>
                <td style="width:105px;text-align: left;"><?php if (isset($promo->img)) : ?><img src="<?php echo SRC_URL.'/images/'.$promo->image->id; ?>" alt="image"  style="width:100x;height:100px;"/><?php endif; ?></td>
                <td><?php echo $promo->amount; ?>&euro;</td>
                <td><?php echo $promo->status; ?></td>
                <td><?php echo $promo->order; ?></td>
                <td><a href="/admin/bazar/up/<?php echo $promo->id; ?>">[&uarr;]</a></td>
                <td><a href="/admin/bazar/down/<?php echo $promo->id; ?>">[&darr;]</a></td>
                <td><a href="/admin/bazar/edit/<?php echo $promo->id; ?>">[Editar]</a></td>
                <td><?php if ($promo->active) : ?>
                <a href="/admin/bazar/active/<?php echo $promo->id; ?>/off">[Ocultar]</a>
                <?php else : ?>
                <a href="/admin/bazar/active/<?php echo $promo->id; ?>/on">[Mostrar]</a>
                <?php endif; ?></td>
                <?php if ($translator) : ?>
                <td><a href="/translate/bazar/edit/<?php echo $promo->id; ?>" >[Traducir]</a></td>
                <?php endif; ?>
                <td><a href="/admin/bazar/remove/<?php echo $promo->id; ?>" onclick="return confirm('Seguro que deseas eliminar este registro?');">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
