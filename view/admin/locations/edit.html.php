<?php

use Goteo\Library\Text;

$location = $this['location'];
?>
<div class="widget">
    <form action="/admin/locations/<?php echo ($this['action'] == 'add') ? 'add' : 'edit/'.$location->id ?>" method="post">
        <p>
            <label for="name">Buscar:</label><br />
            <input type="text" id="location-name" name="name" value="" />
            <span stye="font-style: italic;">Al escribir algo lo busca en el mapa</span>
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
        </table>

        <!-- mapa -->
        <p>Al mover el iconito se modificará la longitud y la latitud.</p>

        <div id="map-canvas">Aqui ira el mapa</div>

        <p>
            <label><input type="checkbox" name="valid" value="1" <?php if ($location->valid) echo 'checked="checked";' ?>/>Dar por revisada</label>
        </p>

        <a class="button" href="/admin/locations">Cancelar / Salir sin guardar</a>
        <input type="submit" name="save" value="Guardar / Aplicar los cambios" />

    </form>
</div>