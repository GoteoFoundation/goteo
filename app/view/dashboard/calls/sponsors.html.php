<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Model;

$call = $vars['call'];
$sponsor = $vars['sponsor'];

if ($vars['action'] == 'add' || $vars['action'] == 'edit') : ?>
<div class="widget">
    <?php if ($vars['action'] == 'add') : ?><h3>Nuevo patrocinador</h3><?php endif; ?>
    <form method="post" action="/dashboard/calls/sponsors/edit" enctype="multipart/form-data" >
        <input type="hidden" name="action" value="<?php echo $vars['action'] ?>" />
        <input type="hidden" name="order" value="<?php echo $sponsor->order ?>" />
        <input type="hidden" name="id" value="<?php echo $sponsor->id; ?>" />

        <p>
            <label for="sponsor-name">Nombre:</label><br />
            <input type="text" name="name" id="sponsor-name" value="<?php echo $sponsor->name; ?>" style="width:500px;" />
        </p>

        <p>
            <label for="sponsor-url">Enlace:</label><br />
            <input type="text" name="url" id="sponsor-url" value="<?php echo $sponsor->url; ?>" style="width:750px;" />
        </p>

        <p>
            <label for="sponsor-image">Logo:</label><br />
            <input type="file" id="sponsor-image" name="image" />
            <?php if (!empty($sponsor->image)) : ?>
                <br />
                <input type="hidden" name="prev_image" value="<?php echo $sponsor->image->id ?>" />
                <img src="<?php echo $sponsor->image->getLink(150, 85) ?>" alt="falta imagen"/>
                <input type="submit" name="image-<?php echo $sponsor->image->hash; ?>-remove" value="Quitar" />
            <?php endif; ?>
        </p>

        <input type="submit" name="save" value="Guardar" />
    </form>
</div>
<?php else : ?>
<a href="/dashboard/calls/sponsors/add" class="button">A&ntilde;adir patrocinador</a>
<div class="widget">
    <?php if (!empty($call->sponsors)) : ?>
    <table>
        <tr>
            <th></th>
            <th>Patrocinador</th>
            <th>Enlace</th>
            <th>Imagen</th>
            <th>Posición</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <?php foreach ($call->sponsors as $spon) : ?>
        <tr>
            <td><a href="/dashboard/calls/sponsors/edit/<?php echo $spon->id ?>">[Editar]</a></td>
            <td><?php echo $spon->name ?></td>
            <td><?php echo $spon->url ?></td>
            <td><?php if ($spon->image instanceof Model\Image) : ?><img src="<?php echo $spon->image->getLink(110, 110); ?>" alt="<?php echo $spon->name ?>" /><?php endif; ?></td>
            <td><?php echo $spon->order ?></td>
            <td><a href="/dashboard/calls/sponsors/up/<?php echo $spon->id ?>">[&uarr;]</a></td>
            <td><a href="/dashboard/calls/sponsors/down/<?php echo $spon->id ?>">[&darr;]</a></td>
            <td><a href="/dashboard/calls/sponsors/delete/<?php echo $spon->id ?>" onclick="return confirm('Seguro que quitamos el patrocinador \'<?php echo $spon->name ?>\' para la convocatoria \'<?php echo $call->name ?>\' ?')">[Quitar]</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else : ?>
    <p>Ning&uacute;n patrocinador</p>
    <?php endif; ?>
</div>
<?php endif; ?>
