<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$project = $this['project'];
$status = $this['statuses'];
?>
<a class="button" href="/admin/commons">Volver a la lista</a>
&nbsp;&nbsp;&nbsp;
<a class="button" href="/admin/commons/add/<?php echo $project->id; ?>">Retorno adicional</a>
<!--
&nbsp;&nbsp;&nbsp;
<a class="button" href="/project/edit/<?php echo $project->id; ?>#rewards" target="blank">Editar en formulario de proyecto</a>
-->
<br /><br />
<div class="widget board">
    <h3><?php echo $project->name; ?> (<?php echo $status[$project->status]; ?>)</h3>
    <?php echo new View('view/project/edit/rewards/view_commons.html.php', $this); ?>
</div>
<?php echo new View('view/project/edit/rewards/commons.js.php'); ?>