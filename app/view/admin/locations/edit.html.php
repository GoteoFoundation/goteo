<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$location = $this['location'];
?>
<div class="widget">
    <form action="/admin/locations/<?php echo ($this['action'] == 'add') ? 'add' : 'edit/'.$location->id ?>" method="post">
        <p>
            <a href="https://maps.google.com/maps?q=<?php echo urlencode($location->name) ?>" target="_blank">Ver en google maps, por nombre</a>
        </p>

        <table>
            <tr>
                <td>
                    <label for="location">Localidad:</label><br />
                    <input type="text" name="location" value="<?php echo $location->location ?>" />
                </td>
                <td>
                    <label for="region">Provincia:</label><br />
                    <input type="text" name="region" value="<?php echo $location->region ?>" />
                </td>
                <td>
                    <label for="country">Pais:</label><br />
                    <input type="text" name="country" value="<?php echo $location->country ?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <label>Latitud:</label>
                    <input type="text" name="lat" id="locLt" value="<?php echo $location->lat ?>" />
                </td>
                <td>
                    <label>Longitud:</label>
                    <input type="text" name="lon" id="locLg" value="<?php echo $location->lon ?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <label>Revisada:</label>
                    <input type="checkbox" name="valid" value="1" <?php if ($location->valid) echo 'checked="checked"'; ?>/>
                </td>
            </tr>
            <tr>
                <td>
                    <a class="button" href="/admin/locations">CANCELAR / Salir sin guardar</a>
                </td>
                <td>
                    <input type="submit" name="save" value="GUARDAR / Aplicar los cambios" />
                </td>
            </tr>
        </table>
    </form>

    <!-- mapa -->
    <?php echo View::get('widget/map.html.php', array('lat'=>$location->lat,'lon'=>$location->lon, 'name'=>$location->name)); ?>

</div>
