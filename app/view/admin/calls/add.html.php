<?php

use Goteo\Library\Text;

?>
<div class="widget">
    <form id="filter-form" action="/call/create" method="post">
        <input type="hidden" name="action" value="continue" />
        <input type="hidden" name="confirm" value="true" />
        <input type="hidden" name="admin" value="admin" />
        <p>
            <label for="call-id">Identificador:</label><br />
            <input type="text" id="call-id" name="name" value="" />
        </p>
        <p>
            <label for="call-user">Convocador:</label><br />
            <select id="call-user" name="caller">
                <option value="">Seleccionar usuario</option>
            <?php foreach ($vars['callers'] as $userId=>$userName) : ?>
                <option value="<?php echo $userId; ?>"><?php echo $userName; ?></option>
            <?php endforeach; ?>
            </select>
        </p>

        <input type="submit" name="create" value="Crear convocatoria" />

    </form>
</div>
