<?php

use Goteo\Library\Text;

$projects = $this['projects'];
?>
<div class="widget">
    <h3>Proyectos seleccionados en esta convocatoria</h3>
    <p><?php echo '<pre>' . print_r($projects, 1) . '</pre>'; ?></p>
</div>

