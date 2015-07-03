<div id="project-selector">
    <?php if (!empty($vars['calls'])) : ?>
        <form id="selector-form" name="selector_form" action="<?php echo '/dashboard/'.$vars['section'].'/'.$vars['option'].'/select'; ?>" method="post">
        <label for="selector">Convocatoria:</label>
        <select id="selector" name="call" onchange="document.getElementById('selector-form').submit();">
        <?php foreach ($vars['calls'] as $call) : ?>
            <option value="<?php echo $call->id; ?>"<?php if ($call->id == $_SESSION['call']->id) echo ' selected="selected"'; ?>><?php echo $call->name; ?></option>
        <?php endforeach; ?>
        </select>
        <!-- un boton para seleccionar si no tiene javascript -->
        </form>
    <?php else : ?>
    <p>No tienes ninguna convocatoria</p>
    <?php endif; ?>
</div>
