<?php
use Goteo\Library\Text,
    Goteo\Model\Location;

// localizaciones válidas asignables
$geolocs = Location::getAllMini();
// quito los detalles
// echo \trace($_SESSION['last_gmaps_response_data']);
?>
<div class="widget board">
    <form id="apply-form" action="/admin/locations/autocheck" method="post">
        <input type="hidden" name="location" value="<?php echo $this['location']; ?>" /><br />
        <input type="hidden" name="geodata" value='<?php echo (!empty($this['geodata'])) ? serialize($this['geodata']) : ''; ?>' /><br />

        <p>
            <label>Usuarios:</label> <?php echo $this['user']; ?><br />
            <label>Localidad en su registro:</label> <strong><?php echo $this['location']; ?></strong><br />
            <label>Obtenido por proceso:</label>  <?php echo \trace($this['geodata']); ?><br />
            <label>Asignar a geolocalización ya existente ya existente<br />
                <select name="geoloc">
                    <option value="">--</option>
                    <?php foreach ($geolocs as $glId=>$glName) {
                        $setted = ($glName == "{$this['geodata']['location']}, {$this['geodata']['region']}, {$this['geodata']['country']}") ? ' selected="selected"' : '';
                        echo '<option value="'.$glId.'"'.$setted.'>'.$glName.'</option>';
                    } ?>
                </select>
            </label>
        </p>
            
        <p>
            <label>Crear: <input type="checkbox" name="create" value="1" checked="checked" /></label> Desmarcar si no parece una localizacion valida o si hay que asignar a una lozacion existente.<br />
        </p>
            
        <p>
            <label>Asignar: <input type="checkbox" name="assign" value="1" checked="checked" /></label> Desmarcar si no se quiere asignar a los usuarios.<br />
        </p>
            
        <p>
            <label>Dar por ilocalizable: <input type="checkbox" name="unlocable" value="1" /></label> Marcar si devuelve error irrecuperable, no grabara la localización y dara a esos usuarios por ilocalizables.<br />
        </p>
        
        <input type="submit" name="apply" value="Aplicar" />
        
    </form>
</div>
