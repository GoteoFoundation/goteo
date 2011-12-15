<?php

use Goteo\Library\Text;

$call = $this['call'];
$projects = $this['projects'];
$available = $this['available'];
$status = $this['status'];
?>
<script type="text/javascript">
    function assignproj (id) {
        document.getElementById('hidproj').value = id;
        document.getElementById('hidform').submit();
    }
</script>
<div class="widget">
    <p>Para asignar otro proyecto, seleccionar y clickar en "Asignar"</p>
    <form method="post" action="/admin/calls/projects/<?php echo $call->id; ?>">
        <input type="hidden" name="operation" value="assign" />
        <p>
            <label for="call-project">Proyecto:</label><br />
            <select id="call-project" name="project">
                <option value="" >Seleccionar el proyecto a asignar</option>
            <?php foreach ($available as $proj) : ?>
                <option value="<?php echo $proj->id; ?>"><?php echo $proj->name . ' ('. $status[$proj->status] . ')'; ?></option>
            <?php endforeach; ?>
            </select>
        </p>
        
        <input type="submit" value="Asignar" />
    </form>
</div>
<div class="widget">
    <h3>Proyectos seleccionados en esta convocatoria</h3>
    <?php if (!empty($projects)) : ?>
    <table>
        <tr>
            <th>Proyecto</th>
            <th>Estado</th>
            <th>Riego</th>
            <th></th>
        </tr>
        <?php foreach ($projects as $proj) : ?>
        <tr>
            <td><?php echo $proj->name ?></td>
            <td><?php echo $status[$proj->status] ?></td>
            <td><?php echo $proj->amount ?></td>
            <td><?php if ($proj->amount <= 0) : ?>
                <a href="#" onclick="if (confirm('Seguro que quitamos el proyecto \'<?php echo $proj->name ?>\' de la convocatoria \'<?php echo $call->name ?>\' ?')) { assignproj('<?php echo $proj->id ?>') }">[Quitar]</a>
            <?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else : ?>
    <p>Ning&uacute;n proyecto</p>
    <?php endif; ?>
</div>
<form id="hidform" method="post" action="/admin/calls/projects/<?php echo $call->id; ?>">
    <input type="hidden" name="operation" value="unassign" />
    <input type="hidden" id="hidproj" name="project" value="" />
</form>

