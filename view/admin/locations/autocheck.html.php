<?php
use Goteo\Library\Text,
    Goteo\Model\Location;

// localizaciones válidas asignables
$geolocs = Location::getAllMini();
// quito los detalles
// echo \trace($_SESSION['last_gmaps_response_data']);
$user = $this['user'];
$geodata = $this['geodata'];
$location = $this['location'];
?>
<div class="widget board">
    <form id="apply-form" action="/admin/locations/autocheck" method="post">
        <input type="hidden" name="location" value="<?php echo $location; ?>" /><br />
        <input type="hidden" name="geodata" value='<?php echo (!empty($geodata)) ? serialize($geodata) : ''; ?>' /><br />

        <p>
            <label>Usuarios:</label> <?php echo $this['cuantos']; if (isset($user)) echo " {$user->name}  ({$user->id})  [{$user->email}]"; ?><br />
            <label>Localidad en su registro:</label> <strong><?php echo $location; ?></strong><br />
            <label>Obtenido por proceso:</label>  <?php echo \trace($geodata); ?><br />
            <label>Asignar a geolocalización ya existente ya existente<br />
                <select name="geoloc">
                    <option value="">--</option>
                    <?php foreach ($geolocs as $glId=>$glName) {
                        $setted = ($glName == "{$geodata['location']}, {$geodata['region']}, {$geodata['country']}") ? ' selected="selected"' : '';
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
            <label>Dar por ilocalizable: <input type="checkbox" name="unlocable" value="1"<?php if (empty($geodata)) echo ' checked="checked"'; ?> /></label> Marcar si devuelve error irrecuperable, no grabara la localización y dara a esos usuarios por ilocalizables.<br />
        </p>
        
        <input type="submit" name="apply" value="Aplicar" />
        
    </form>
</div>

<div class="widget board">
    <form id="apply-form" action="/admin/locations/autocheck" method="post">
        <input type="hidden" name="location" value="<?php echo $location; ?>" /><br />
        <p>
            <label>Cambiar la localidad a:<br />
                <input type="text" name="newlocation" value="<?php echo $location; ?>" style="width: 500px;" />
            </label>
            <input type="submit" name="change" value="Cambiar" />
        </p>
        
    </form>
    
    <p>
        <a href="https://maps.google.com/maps?q=<?php echo urlencode($location) ?>" target="_blank">Ver en google maps</a>
    </p>
    
    <?php if (isset($user)) : ?>
    <p>
        <a href="/user/profile/<?php echo $user->id ?>" target="_blank">Ver usuario</a>
    </p>
    <?php endif; ?>
</div>
