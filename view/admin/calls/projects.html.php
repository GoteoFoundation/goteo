<?php

use Goteo\Library\Text;

$call = $this['call'];
$projects = $this['projects'];
//$available = $this['available']; // quitamos los relacionados
$status = $this['status'];
?>
<script type="text/javascript">
    function assignproj (id) {
        document.getElementById('hidproj').value = id;
        document.getElementById('operation').value = 'assign';
        document.getElementById('hidform').submit();
    }
    function unassignproj (id) {
        document.getElementById('hidproj').value = id;
        document.getElementById('operation').value = 'unassign';
        document.getElementById('hidform').submit();
    }
</script>
<div class="widget">
    <h3>Proyectos seleccionados</h3>
    <?php if (!empty($projects)) : ?>
    <table>
        <tr>
            <th></th>
            <th>Proyecto</th>
            <th>Estado</th>
            <th>Localidad</th>
            <th>Riego</th>
            <th></th>
        </tr>
        <?php foreach ($projects as $proj) : ?>
        <tr>
            <td><a href="/admin/projects/?proj_name=<?php echo urlencode($proj->name) ?>&status=<?php echo $proj->status ?>&name=&category=&node=" target="_blank" title="Gestionar proyecto">[Gestionar]</a></td>
            <td><a href="/project/<?php echo $proj->id ?>" target="_blank" title="Ver proyecto"><?php echo $proj->name ?></a></td>
            <td><?php echo $status[$proj->status] ?></td>
            <td><?php echo $proj->location ?></td>
            <td><?php echo $proj->amount_call ?></td>
            <td><?php if ($proj->amount_call <= 0) : ?>
                <a href="#" onclick="if (confirm('Seguro que quitamos el proyecto, <?php echo addslashes(str_replace('"', "'", $proj->name)) ?>, de la convocatoria, <?php echo addslashes(str_replace('"', "'", $call->name)) ?> ?')) { unassignproj('<?php echo $proj->id ?>') }">[Quitar]</a>
            <?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else : ?>
    <p>Ning&uacute;n proyecto</p>
    <?php endif; ?>
</div>
<form id="hidform" method="post" action="/admin/calls/projects/<?php echo $call->id; ?>">
    <input type="hidden" id="operation" name="operation" value="" />
    <input type="hidden" id="hidproj" name="project" value="" />
</form>
<?php
/*
 * Quitamos los relacionados
 * 
<div class="widget">
    <h3>Proyectos relacionados (no seleccionados)</h3>
    <?php if (!empty($available)) : ?>
    <table>
        <tr>
            <th></th>
            <th>Proyecto</th>
            <th>Estado</th>
            <th>Localidad</th>
            <th></th>
        </tr>
        <?php foreach ($available as $proj) : ?>
        <tr>
            <td><a href="/admin/projects/?proj_name=<?php echo urlencode($proj->name) ?>&status=<?php echo $proj->status ?>&name=&category=&node=" target="_blank" title="Gestionar proyecto">[Gestionar]</a></td>
            <td><a href="/project/<?php echo $proj->id ?>" target="_blank" title="Ver proyecto"><?php echo $proj->name ?></a></td>
            <td><?php echo $status[$proj->status] ?></td>
            <td><?php echo $proj->location ?></td>
            <td><a href="#" onclick="assignproj('<?php echo $proj->id ?>')">[Seleccionar]</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else : ?>
    <p>Ning&uacute;n proyecto</p>
    <?php endif; ?>
</div>
*/
