<?php
use Goteo\Library\Text;
?>
<div class="widget board">
    <form id="apply-form" action="/admin/locations/autocheck" method="post">
        

        <p>
            <label>Usuarios:</label> <?php echo $this['user']; ?><br />
            
            <label>Localidad en su registro:</label> <?php echo $this['location']; ?><br />
            <input type="text" name="location" value="<?php echo $this['location']; ?>" style="width:600px;" /><br />
            
            <label>Obtenido por proceso:</label>  <?php echo \trace($this['geodata']); ?><br />
            <input type="text" name="geodata" value="<?php echo serialize($this['geodata']); ?>" style="width:600px;" /><br />
            
            <label>Crear: <input type="checkbox" name="create" value="1" checked="checked" /></label> Desmarcar si no parece una localizacion valida.<br />
            <label>Asignar: <input type="checkbox" name="assign" value="1" checked="checked" /></label> Desmarcar si se desmarca el de arriba.<br />
            <label>Dar por ilocalizable: <input type="checkbox" name="unlocable" value="1" /></label> Marcar si devuelve error irrecuperable.<br />
        </p>
        
        <input type="submit" name="apply" value="Aplicar" />
        
        
        <p>
            Detalles:<br />
            <?php echo \trace($_SESSION['last_gmaps_response_data']); ?>
        </p>
        
    </form>
</div>
